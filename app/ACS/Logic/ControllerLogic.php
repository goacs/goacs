<?php

declare(strict_types=1);


namespace App\ACS\Logic;


use App\ACS\Context;
use App\ACS\Entities\Task;
use App\ACS\Request\GetParameterNamesRequest;
use App\ACS\Request\GetParameterValuesRequest;
use App\ACS\Request\InformRequest;
use App\ACS\Request\SetParameterValuesRequest;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\InformResponse;
use App\ACS\Types;
use App\Models\Device;
use App\Models\DeviceParameter;
use League\CommonMark\Block\Element\ThematicBreak;

class ControllerLogic
{
    const SESSION_NEW = 'new';

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

        $this->runTasks();
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

        $this->context->device->new = true;
//        $this->context->device->new = $this->context->deviceModel->wasRecentlyCreated;
    }

    private function processEmptyResponse()
    {
        if($this->context->device->new === true) {
            $this->context->response->setContent(
            //Maybe need to query at first only for nextlevel params
            //then chun response to next GPN requests
                (new GetParameterNamesRequest($this->context, $this->context->device->root, false))->getBody()
            )->send();
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
        $body = (new InformResponse($this->context))->getBody();
        $this->context->response->setContent($body)->send();

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
        if($this->context->device->new) {
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
    }

    private function processAddObjectResponse()
    {
    }

    private function processDeleteObjectResponse()
    {
    }

    private function runTasks()
    {
        /** @var Task $task */
        $task = $this->context->tasks->shift();
        if($task === null) {
            $this->endSession();
            return;
        }

        switch ($task->name) {
            case Types::GetParameterValues:
                $request = new GetParameterValuesRequest($this->context);
                $request->setParameters($task->payload['parameters']);
                $this->context->response->setContent($request->getBody())->send();
                break;

            case Types::SetParameterValues:
                $setterLogic = new ParameterSetterLogic($this->context);
                $request = new SetParameterValuesRequest($this->context);
        }

    }

    private function endSession()
    {
        //End session end response empty
    }
}
