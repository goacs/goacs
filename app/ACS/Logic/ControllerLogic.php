<?php

declare(strict_types=1);


namespace App\ACS\Logic;


use App\ACS\Context;
use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Entities\Task;
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
use App\ACS\Response\FaultResponse;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetRPCMethodsACSResponse;
use App\ACS\Response\InformResponse;
use App\ACS\Response\TransferCompleteResponse;
use App\ACS\Types;
use App\Models\Device;
use App\Models\DeviceParameter;
use App\Models\Fault;
use Illuminate\Support\Collection;

class ControllerLogic
{
    const GET_PARAMETER_VALUES_CHUNK_SIZE = 4;

    /**
     * @var Context
     */
    private Context $context;

    public function __construct(Context $context) {
        $this->context = $context;
    }

    public function process() {

        switch ($this->context->bodyType) {
            case Types::INFORM:
                $this->processInformRequest();
                $this->loadDeviceTasks();
                break;

            case Types::GetRPCMethodsRequest:
                $this->processGetRPCMethodsRequest();
                break;

            case Types::EMPTY:
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

            case Types::FaultResponse:
                $this->processFault();
                break;

        }

        $this->runTasks();

        if($this->context->cpeRequest !== null) {
            $this->context
                ->response
                ->setContent(
                    $this->context->acsResponse->getBody()
                )
                ->send();
        } else if($this->context->acsRequest !== null) {
            $this->context->response
                ->setContent(
                    $this->context->acsRequest->getBody()
                )
                ->send();
        }

        $this->context->storeToSession();
    }

    private function updateDeviceData(): void {
        $this->context->deviceModel = Device::updateOrCreate(
            [
                'serial_number' => $this->context->device->serialNumber,
            ],
            [
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
        if($this->context->new === true || $this->context->boot === true) {
            $task = new Task(Types::GetParameterNames);
            $task->setPayload([
                'parameter' => $this->context->device->root
            ]);

            $this->context->tasks->addTask($task);
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
        //TODO: add to check BOOT/BOOTSTRAP flags

        $task = new Task(Types::INFORMResponse);
        $this->context->tasks->addTask($task);
    }

    private function processGetParameterNamesResponse()
    {
        /** @var GetParameterNamesResponse $getParameterNamesResponse */
        $getParameterNamesResponse = $this->context->cpeResponse;

        if($this->context->tasks->isNextTask(Types::GetParameterNames) === false) {
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
            $this->loadGlobalTasks(Types::GetParameterValuesResponse);
            $this->compareAndProcessObjectParameters();
            $this->processSetPII();
            $this->compareAndProcessSetParameters();
        }

        /*
         * Przemyśleć, czy nie wykonywać tutaj skryptów
         */
    }

    private function compareAndProcessObjectParameters() {
        dump("comparing object params");
//        $dbParameters = ParameterValuesCollection::fromEloquent($this->context->deviceModel->parameters()->get());
        $parameterService = new DeviceParametersLogic($this->context->deviceModel);
        $dbParameters = $parameterService->combinedDeviceParametersWithTemplates();
        $sessionParameters = $this->context->parameterValues;

//        $parametersToAdd = $dbParameters->diff($sessionParameters)->filterByFlag('object')->filterCanInstance();
//        $parametersToAdd2 = $dbParameters->diff($sessionParameters)->filterByFlag('object')->filterInstances();

        $parametersToAdd = $parameterService->getParametersToCreateInstance($sessionParameters, $dbParameters);
//        dump("AddObject count: ".$parametersToAdd->count());
        /** @var ParameterValueStruct $parameter */
        foreach ($parametersToAdd as $parameter) {
            dump("AddObject Parameter: ".$parameter->name);
            $task = new Task(Types::AddObject);
            $task->setPayload(['parameter' => $parameter->name]);
            $this->context->tasks->addTask($task);
        }
    }

    private function processAddObjectResponse()
    {
        dump("AddObject Response");
        $prevTask = $this->context->tasks->prevTask();
        if($prevTask->name !== Types::AddObject) {
            //log
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


    }

    private function processDeleteObjectResponse()
    {
    }

    private function runTasks()
    {
        /** @var Task $task */
        $task = $this->context->tasks->nextTask();
        if($task === null) {
            $this->endSession();
            return;
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
                $request = new DownloadRequest($this->context);
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
                } catch (SandboxException $exception) {
                    //TODO Save to db as fault
                }

                break;
        }

        $task->done();

    }

    private function processGetRPCMethodsRequest()
    {
        $this->context->acsResponse = new GetRPCMethodsACSResponse($this->context);
    }

    private function endSession()
    {
        $root = $this->context->device->root;
        if($param = $this->context->parameterValues->get($root.'ManagementServer.ConnectionRequestUsername')?->value) {
            $this->context->deviceModel->connection_request_user = $param;
        }

        if($param = $this->context->parameterValues->get($root.'ManagementServer.ConnectionRequestPassword')?->value) {
            $this->context->deviceModel->connection_request_password = $param;
        }

        $this->context->deviceModel->save();
    }

    private function calculatePIIValue(): int {
        return rand(10000, 40000);
    }

    private function processSetPII()
    {
        if($pvs = $this->context->parameterValues->get($this->context->device->root.'ManagementServer.PeriodicInformInterval')) {
            $pvs->value = (string)$this->calculatePIIValue();
            DeviceParameter::setParameter($this->context->deviceModel->id, $pvs->name, $pvs->value);


            /* Next task after this are compare params to generate spv tasks...
            $task = new Task(Types::SetParameterValues);
            $task->setPayload(['parameters' => new ParameterValuesCollection([$pvs])]);
            $this->context->tasks->addTask($task);*/
        }
    }

    private function loadDeviceTasks()
    {
        $tasks = $this->context->deviceModel->tasks;
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

        $diffParameters = $dbParameters->diff($sessionParameters)->filterByFlag('send')->filterByFlag('object', false);

        foreach($diffParameters->chunk(10) as $chunk) {
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
        Fault::fromFaultResponse($this->context->deviceModel, $response);
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

}
