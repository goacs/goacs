<?php

declare(strict_types=1);


namespace App\ACS\Logic;


use App\ACS\Context;
use App\ACS\Entities\Task;
use App\ACS\Request\GetParameterNamesRequest;
use App\ACS\Request\GetParameterValuesRequest;
use App\ACS\Request\InformRequest;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\InformResponse;
use App\ACS\Types;
use App\Models\Device;

class ControllerLogic
{
    const SESSION_NEW = 'new';

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
                $this->updateDeviceData();
                $body = (new InformResponse($this->context))->getBody();
                $this->context->response->setContent($body)->send();
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
            ]
        );

        $this->context->device->new = true;
//        $this->context->device->new = $this->context->deviceModel->wasRecentlyCreated;
    }

    private function processEmptyResponse()
    {
        if($this->context->device->new === true) {
            $this->context->response->setContent(
                (new GetParameterNamesRequest($this->context, $this->context->device->root))->getBody()
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
    }

    private function processGetParameterNamesResponse()
    {
        /** @var GetParameterNamesResponse $getParameterNamesResponse */
        $getParameterNamesResponse = $this->context->cpeResponse;

        foreach($getParameterNamesResponse->parameters->chunk(50) as $chunk)
        {
            $task = new Task(Types::GetParameterValues);
            $task->setPayload([
                'parameters' => $chunk
            ]);
            $this->context->addTask($task);
        }
    }

    private function runTasks()
    {
        $task = $this->context->tasks->shift();
        if($task === null) {
            return;
        }

        switch ($task->name) {
            case Types::GetParameterValues:
                $request = new GetParameterValuesRequest($this->context);
                $request->setParameters($task->payload['parameters']);
                $this->context->response->setContent($request->getBody())->send();
                break;
        }

    }

    private function processGetParametersValuesResponse()
    {
    }

    private function processAddObjectResponse()
    {
    }

    private function processDeleteObjectResponse()
    {
    }
}
