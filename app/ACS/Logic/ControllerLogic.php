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
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetRPCMethodsACSResponse;
use App\ACS\Response\InformResponse;
use App\ACS\Response\TransferCompleteResponse;
use App\ACS\Types;
use App\Models\Device;
use App\Models\DeviceParameter;

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
                $this->processGetParametersValuesResponse();
                break;

            case Types::AddObjectResponse:
                $this->processAddObjectResponse();
                break;

            case Types::DeleteObjectResponse:
                $this->processDeleteObjectResponse();
                break;

        }
        //Może zamiast wysyłać acsReq z taska, to lepiej sterować w context acsRequest a następnie zrobić tak:
        /*
         * if(context->cpeRequest) {
         *      send response and return
         * } else if(context->acsRequest) {
         *      send request and return
         * }
         */
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

        $this->context->new = true;
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
        $task->setPayload([
            'parameter' => $this->context->device->root
        ]);

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

    private function processGetParametersValuesResponse()
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

        if($this->context->boot) {
            $this->compareAndProcessObjectParameters();
        }

        if($this->context->tasks->prevTask()?->name === Types::AddObject) {
            DeviceParameter::massUpdateOrInsert(
                $this->context->deviceModel,
                $this->context->cpeResponse->parameters
            );
        }


        if($this->context->tasks->isNextTask(Types::GetParameterValues) === false) {
            $this->processSetPII();

        }

        /*
         * Przemyśleć, czy nie wykonywać tutaj skryptów
         */
    }

    private function compareAndProcessObjectParameters() {
        $dbParameters = (new ParameterValuesCollection())->push($this->context->deviceModel->parameters()->get());
        $sessionParameters = $this->context->parameterValues;

        $parametersToAdd = $dbParameters->diff($sessionParameters)->filterByFlag('object');
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
                'parameters' => [
                    (new ParameterInfoStruct())->name => $path.".".$addObjectResponse->getInstanceNumber()."."
                ]
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
                $request = new SetParameterValuesRequest($this->context);
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
        //End session end response empty
    }

    private function calculatePIIValue(): int {
        return rand(10000, 40000);
    }

    private function processSetPII()
    {
        $pvs = new ParameterValueStruct();
        $pvs->name = $this->context->device->root.'ManagementServer.PeriodicInformInterval';
        $pvs->value = (string) $this->calculatePIIValue();

        DeviceParameter::setParameter($this->context->deviceModel->id, $pvs->name, $pvs->value);

        $task = new Task(Types::SetParameterValues);
        $task->setPayload(['parameters' => [
            $pvs
        ]]);
        $this->context->tasks->addTask($task);
    }

}
