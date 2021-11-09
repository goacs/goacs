<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Context;
use App\ACS\Entities\Task;
use App\ACS\Request\InformRequest;
use App\ACS\Response\ErrorResponse;
use App\ACS\Types;
use App\Models\Device;
use App\Models\Log;

class InformRequestProcessor extends Processor
{

    public function __invoke()
    {
        /** @var InformRequest $infom */
        $infom = $this->context->cpeRequest;
        collect($infom->parametersList)->each(fn($item) => $this->context->parameterValues->put($item->name, $item));
        $this->context->device->root = "InternetGatewayDevice.";
        if($this->context->parameterValues->first() !==  null) {
            $this->context->device->root = explode(".", $this->context->parameterValues->first()->name)[0].".";
        }

        $this->updateDeviceData();

        if($this->sessionExists()) {
            $this->context->provisioningCurrentState = Context::PROVISIONING_STATE_ERROR;
            $this->context->acsResponse = new ErrorResponse($this->context, 'Session DUP');
            Log::logError($this->context->deviceModel,  'Session DUP', '100400', [
                'faultString' => 'SESSION ID: '. \Cache::get("SESSID_".$this->context->device->serialNumber)
            ]);
            return;
        }

        \Cache::put("SESSID_".$this->context->device->serialNumber, $this->context->session()->getId(),(5*60));

        $task = new Task(Types::INFORMResponse);
        $this->context->tasks->addTask($task);
        $this->loadGlobalTasks(Types::INFORM);
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
        $this->context->provisioningCurrentState = $this->context->new === true ? Context::PROVISIONING_STATE_NEW : Context::PROVISIONING_STATE_READPARAMS;
//        $this->context->device->new = $this->context->deviceModel->wasRecentlyCreated;
    }

    private function sessionExists(): bool {
        return \Cache::has("SESSID_".$this->context->device->serialNumber);
    }
}
