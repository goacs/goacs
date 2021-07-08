<?php

declare(strict_types=1);


namespace App\ACS\Logic;


use App\ACS\Context;
use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Entities\Task;
use App\ACS\Events\ParameterLookupDone;
use App\ACS\Logic\Script\Sandbox;
use App\ACS\Logic\Script\SandboxException;
use App\ACS\Request\AddObjectRequest;
use App\ACS\Request\DeleteObjectRequest;
use App\ACS\Request\DownloadRequest;
use App\ACS\Request\GetParameterNamesRequest;
use App\ACS\Request\GetParameterValuesRequest;
use App\ACS\Request\InformRequest;
use App\ACS\Request\SetParameterValuesRequest;
use App\ACS\Response\AddObjectResponse;
use App\ACS\Response\DownloadResponse;
use App\ACS\Response\FaultResponse;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetRPCMethodsACSResponse;
use App\ACS\Response\InformResponse;
use App\ACS\Response\TransferCompleteResponse;
use App\ACS\Types;
use App\ACS\XML\XMLParser;
use App\Models\Device;
use App\Models\DeviceParameter;
use App\Models\Log;
use App\Models\File;
use App\Models\Setting;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ControllerLogic
{
    const GET_PARAMETER_VALUES_CHUNK_SIZE = 4;

    const SET_PARAMETER_VALUES_CHUNK_SIZE = 10;

    /**
     * @var Context
     */
    private Context $context;

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;


    public function __construct(Context $context, Dispatcher $dispatcher) {
        $this->context = $context;
        $this->dispatcher = $dispatcher;
    }

    public function process(): Response {

        switch ($this->context->bodyType) {
            case Types::INFORM:
                $this->processInformRequest();
                $this->loadGlobalTasks(Types::INFORM);
//                $this->loadDeviceTasks();
                break;

            case Types::GetRPCMethodsRequest:
                $this->processGetRPCMethodsRequest();
                break;

            case Types::EMPTY:
                $this->loadGlobalTasks(Types::EMPTY);
                $this->processEmptyResponse();
               break;

            case Types::GetParameterNamesResponse:
                $this->processGetParameterNamesResponse();
                break;

            case Types::GetParameterValuesResponse:
                $this->processGetParameterValuesResponse();
                break;

            case Types::SetParameterValuesResponse:
                $this->processSetParameterValuesResponse();
                break;

            case Types::AddObjectResponse:
                $this->processAddObjectResponse();
                break;

            case Types::DeleteObjectResponse:
                $this->processDeleteObjectResponse();
                break;

            case Types::DownloadResponse:
                $this->processDownloadResponse();
                break;

            case Types::TransferComplete:
                $this->processTransferCompleteRequest();
                break;

            case Types::FaultResponse:
                $this->processFault();
                break;

        }

        Log::logConversation($this->context->deviceModel,
            'device',
            $this->context->bodyType,
            (string) $this->context->request->getContent(),
        );

        $this->runTasks();

        if($this->context->cpeRequest !== null) {
            $this->context
                ->response
                ->setContent(
                    XMLParser::normalize($this->context->acsResponse->getBody())
                )
                ->header('SOAPServer', 'GoACS')
                ->header('Server', 'GoACS')
                ->header('Content-Type', 'text/xml; encoding="utf-8"')
            //    ->send()
            ;

            Log::logConversation($this->context->deviceModel,
                'acs',
                $this->context->acsResponse->getBaseName(),
                (string) $this->context->acsResponse->getBody(),
            );

        } else if($this->context->acsRequest !== null) {
            $this->context
                ->response
                ->setContent(
                    XMLParser::normalize($this->context->acsRequest->getBody())
                )
                ->header('SOAPServer', 'GoACS')
                ->header('Server', 'GoACS')
                ->header('Content-Type', 'text/xml; encoding="utf-8"')
            //    ->send()
            ;

            Log::logConversation($this->context->deviceModel,
                'acs',
                $this->context->acsRequest->getBaseName(),
                (string) $this->context->acsRequest->getBody(),
            );
        }

        $this->context->storeToSession();
        return $this->context->response;
    }

    private function runTasks()
    {
        $this->loadDeviceTasks();
        /** @var Task $task */
        $task = $this->context->tasks->nextTask();
        if($task === null) {
            $this->endSession();
            return;
        }

        if($task->isOnRequest($this->context->bodyType) === false) {
            $this->context->tasks->add(clone $task);
            $task->done();
            $this->runTasks();
        }


        switch ($task->name) {
            case Types::INFORMResponse:
                $acsResponse = (new InformResponse($this->context));
                $this->context->acsResponse = $acsResponse;
                break;

            case Types::GetParameterNames:
                $acsRequest = (new GetParameterNamesRequest($this->context, $task->payload['parameter']));
                $this->context->acsRequest = $acsRequest;
                break;


            case Types::GetParameterValues:
                $request = new GetParameterValuesRequest($this->context);
                $request->setParameters($task->payload['parameters']);
                $this->context->acsRequest = $request;
                break;

            case Types::SetParameterValues:
                $request = new SetParameterValuesRequest($this->context, $task->payload['parameters']);
                $this->context->acsRequest = $request;
                break;

            case Types::AddObject:
                $request = new AddObjectRequest($this->context, $task->payload['parameter']);
                $this->context->acsRequest = $request;
                break;

            case Types::DeleteObject:
                $request = new DeleteObjectRequest($this->context, $task->payload['parameter']);
                $this->context->acsRequest = $request;
                break;

            case Types::Download:
                $fileData = $this->getFileData($task->payload['filename']);
                $request = new DownloadRequest($this->context, $task->payload['filetype'], $fileData['url'], $fileData['size']);
                $this->context->acsRequest = $request;
                break;

            case Types::TransferCompleteResponse:
                $response = new TransferCompleteResponse($this->context);
                $this->context->acsResponse = $response;
                break;

            case Types::RunScript:
                $sandbox = new Sandbox($this->context, $task->payload['script']);
                try {
                    $sandbox->run();
                    Log::logConversation($this->context->deviceModel,
                        'acs',
                        'Run Script',
                        (string) $task->payload['script'],
                    );
                } catch (SandboxException $exception) {
                    $fault = $this->context->deviceModel->faults()->make();
                    $fault->full_xml = $exception->getTraceAsString();
                    $fault->message = $exception->getMessage();
                    $fault->code = '100020';
                    $fault->save();
                }
                $task->done();
                $this->runTasks();
                break;
        }

        $task->done();

    }

    private function updateDeviceData(): void {
        $this->context->deviceModel = Device::updateOrCreate(
            [
                'serial_number' => $this->context->device->serialNumber,
            ],
            [
                'software_version' => $this->context->parameterValues->get($this->context->device->root .'DeviceInfo.SoftwareVersion')?->value,
                'product_class' => $this->context->device->productClass,
                'oui' => $this->context->device->oui,
                'connection_request_url' => $this->context->parameterValues->get($this->context->device->root . "ManagementServer.ConnectionRequestURL")->value,
                'updated_at' => now(),
            ]
        );

        $this->context->new = $this->context->deviceModel->wasRecentlyCreated;
//        $this->context->device->new = $this->context->deviceModel->wasRecentlyCreated;
    }

    private function processEmptyResponse()
    {
//        dump("EMPTY RESPONSE");
//
//        if($this->context->tasks->hasTaskOfType(Types::Download)) {
//            $task = $this->context->tasks->getTaskOfType(Types::Download);
//            $fileUrl = $this->getFileURL($task->payload['filename']);
//            $request = new DownloadRequest($this->context, $task->payload['filetype'], $fileUrl);
//            $this->context->acsRequest = $request;
//            $task->done();
//            return;
//        }

        if($this->context->new === true || $this->context->provision === true || $this->context->lookupParameters) {
            $task = new Task(Types::GetParameterNames);
            $task->setPayload([
                'parameter' => $this->context->device->root
            ]);

            $this->context->tasks->addTask($task);
            return;
        }

    }

    private function processInformRequest()
    {
        /** @var InformRequest $infom */
        $infom = $this->context->cpeRequest;
        collect($infom->parametersList)->each(fn($item) => $this->context->parameterValues->put($item->name, $item));
        $this->context->device->root = "InternetGatewayDevice.";
        if($this->context->parameterValues->first() !==  null) {
            $this->context->device->root = explode(".", $this->context->parameterValues->first()->name)[0].".";
        }

        $this->updateDeviceData();

        $task = new Task(Types::INFORMResponse);
        $this->context->tasks->addTask($task);
    }

    private function processGetParameterNamesResponse()
    {
        /** @var GetParameterNamesResponse $getParameterNamesResponse */
        $getParameterNamesResponse = $this->context->cpeResponse;

        if($this->context->tasks->isNextTask(Types::GetParameterNames) === false) {
            dump("GPN CHUNKING");
            $filteredParameters = $getParameterNamesResponse->parameters->filterByChunkCount(2,2)->filterEndsWithDot();
            foreach ($filteredParameters->chunk(self::GET_PARAMETER_VALUES_CHUNK_SIZE) as $chunk) {
                $task = new Task(Types::GetParameterValues);
                $task->setPayload([
                    'parameters' => $chunk
                ]);
                $this->context->tasks->addTask($task);
            }
        }
    }

    private function processGetParameterValuesResponse()
    {
        if($this->context->new) {
            //Save Parameters in db
            DeviceParameter::massUpdateOrInsert($this->context->deviceModel, $this->context->cpeResponse->parameters);

            if($this->context->tasks->isNextTask(Types::GetParameterValues) === false) {
                //Save object parameters
                DeviceParameter::massUpdateOrInsert(
                    $this->context->deviceModel,
                    $this->context->parameterInfos->filterEndsWithDot()->toParameterValuesCollecton()
                );
            }
        }

        if($this->context->tasks->prevTask()?->name === Types::AddObject) {
            DeviceParameter::massUpdateOrInsert(
                $this->context->deviceModel,
                $this->context->cpeResponse->parameters
            );
        }


        if($this->context->tasks->isNextTask(Types::GetParameterValues) === false) {
//            dump("PROVSION MODE", $this->context->provision);
            if($this->context->lookupParameters === true) {
                \Cache::put(
                    Context::LOOKUP_PARAMS_PREFIX.$this->context->device->serialNumber,
                    $this->context->parameterValues,
                    now()->addMinutes(15)
                );
                $this->dispatcher->dispatch(new ParameterLookupDone($this->context->deviceModel, $this->context->parameterValues));
            }

            if($this->context->provision === true || $this->context->new === true) {
                $root = $this->context->device->root;
                $settingsUsername = Setting::getValue('connection_request_username');
                $settingsPassword = Setting::getValue('connection_request_password');
                DeviceParameter::setParameter($this->context->deviceModel->id, $root.'ManagementServer.ConnectionRequestUsername', $settingsUsername, 'RWS', 'xsd:string');
                DeviceParameter::setParameter($this->context->deviceModel->id, $root.'ManagementServer.ConnectionRequestPassword', $settingsPassword, 'RWS', 'xsd:string');

                $this->loadGlobalTasks(Types::GetParameterValuesResponse);
                $this->runTasks();
                $this->compareAndProcessObjectParameters();
                $this->processSetPII();
                $this->compareAndProcessSetParameters();
            }
        }

        /*
         * Przemyśleć, czy nie wykonywać tutaj skryptów
         */
    }

    private function compareAndProcessObjectParameters() {
        $parameterService = new DeviceParametersLogic($this->context->deviceModel);
        $dbParameters = $parameterService->combinedDeviceParametersWithTemplates();
        dump("DBParams", $dbParameters->count());
        $sessionParameters = $this->context->parameterValues;
        dump("session params", $sessionParameters->count());

        $parametersToAdd = $parameterService
            ->getParametersToCreateInstance($sessionParameters, $dbParameters)
            ->sortBy('name');

        dump("Object params to add", $parametersToAdd);
        /** @var ParameterValueStruct $parameter */
        foreach ($parametersToAdd as $parameter) {
            $task = new Task(Types::AddObject);
            $task->setPayload(['parameter' => $parameter->name]);
            $this->context->tasks->addTask($task);
        }
    }

    private function processAddObjectResponse()
    {
        $prevTask = $this->context->tasks->prevTask();
        if($prevTask->name !== Types::AddObject) {
            \Log::error('AddObjectResponse out of order!', ['context' => $this->context]);
            return;
        }
        /** @var AddObjectResponse $addObjectResponse */
        $addObjectResponse = $this->context->cpeResponse;
        $path = $prevTask->payload['parameter'];
        $gpvTask = new Task(Types::GetParameterValues);

        $gpvTask->setPayload(
            [
                'parameters' => new Collection([
                    (new ParameterInfoStruct())->name => $path.".".$addObjectResponse->getInstanceNumber()."."
                ]),
            ]
        );

        //TODO Add to task list...

    }

    private function processDeleteObjectResponse()
    {
        $prevTask = $this->context->tasks->prevTask();
        if($prevTask->name !== Types::DeleteObject) {
            \Log::error('DeleteObjectResponse out of order!', ['context' => $this->context]);
            return;
        }
        $path = $prevTask->payload['parameter'];
        $this->context->deviceModel->parameters()->pathset($path)->delete();
    }

    private function processDownloadResponse()
    {
//        /** @var DownloadResponse $downloadResponse */
//        $downloadResponse = $this->context->cpeResponse;
    }

    private function processTransferCompleteRequest()
    {
        $this->context->acsResponse = new TransferCompleteResponse($this->context);
    }

    private function processGetRPCMethodsRequest()
    {
        $this->context->acsResponse = new GetRPCMethodsACSResponse($this->context);
    }

    private function endSession()
    {
        $root = $this->context->device->root;
        if($param = $this->context->parameterValues->get($root.'DeviceInfo.ProductClass')?->value) {
            $this->context->deviceModel->product_class = $param;
        }

        if($param = $this->context->parameterValues->get($root.'DeviceInfo.SoftwareVersion')?->value) {
            $this->context->deviceModel->software_version = $param;
        }

        if($param = $this->context->parameterValues->get($root.'DeviceInfo.HardwareVersion')?->value) {
            $this->context->deviceModel->hardware_version = $param;
        }

        $this->context->deviceModel->save();
        $this->context->flushSession();
    }

    private function calculatePIIValue(): int {
        if(($spread = Setting::getValue('pii')) !== '')
        {
            [$min, $max] = explode('-', $spread);
        }
        return rand((int)$min, (int)$max);
    }

    private function processSetPII()
    {
        if($pvs = $this->context->parameterValues->get($this->context->device->root.'ManagementServer.PeriodicInformInterval')) {
            $pvs->value = (string)$this->calculatePIIValue();
            DeviceParameter::setParameter($this->context->deviceModel->id, $pvs->name, $pvs->value);
        }
    }

    private function loadDeviceTasks()
    {
        $tasks = $this->context->deviceModel->tasks()->get();
        foreach ($tasks as $task) {
            $this->context->tasks->addTask($task->toACSTask());
            $task->delete();
        }
    }

    private function compareAndProcessSetParameters()
    {
        $parameterService = new DeviceParametersLogic($this->context->deviceModel);
        $dbParameters = $parameterService->combinedDeviceParametersWithTemplates();
        $sessionParameters = $this->context->parameterValues;

        $diffParameters = $dbParameters
            ->diff($sessionParameters)
            ->filterByFlag('send')
            ->filterByFlag('object', false)
//            ->sortBy('name')
        ;

        dump("Diff params to set", $diffParameters);

        foreach($diffParameters->chunk(self::SET_PARAMETER_VALUES_CHUNK_SIZE) as $chunk) {
            $task = new Task(Types::SetParameterValues);
            $task->setPayload(['parameters' => $chunk]);
            $this->context->tasks->addTask($task);
        }
    }

    private function loadGlobalTasks(string $on_request)
    {
        $tasks = \App\Models\Task::where([
            'for_type' => \App\Models\Task::TYPE_GLOBAL,
            'on_request' => $on_request
        ])->get();

        foreach ($tasks as $task) {
            $this->context->tasks->addTask($task->toACSTask());
            if($task->infinite === false) {
                $task->delete();
            }
        }
    }

    private function processFault()
    {
        /** @var FaultResponse $response */
        $response = $this->context->cpeResponse;
        Log::fromFaultResponse($this->context->deviceModel, $response);
    }

    private function processSetParameterValuesResponse()
    {
        $prevTask = $this->context->tasks->prevTask();
        if($prevTask !== null && $prevTask->name === Types::SetParameterValues) {
            $parameters = $prevTask->payload['parameters'];
            /** @var ParameterValueStruct $parameter */
            foreach ($parameters as $parameter) {
                $this->context->parameterValues->put($parameter->name, $parameter);
            }
        }
    }

    private function getFileData(string $filename): array {
        $file = File::whereName($filename)->first();
        return [
            'url' => \Storage::disk($file->disk)->url($file->filepath),
            'size' => $file->size,
        ];
    }

}
