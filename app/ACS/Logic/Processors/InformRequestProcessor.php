<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Context;
use App\ACS\Entities\Tasks\Task;
use App\ACS\Request\InformRequest;
use App\ACS\Response\ErrorResponse;
use App\ACS\Types;
use App\Models\Device;
use App\Models\Log;
use App\Models\Setting;

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

        $this->context->deniedParameters = $this->context->provision->getDeniedParameters();

        if($this->sessionExists()) {
            $this->context->provisioningCurrentState = Context::PROVISIONING_STATE_ERROR;
            $this->context->acsResponse = new ErrorResponse($this->context, 'Session DUP');
            Log::logError($this->context,  'Session DUP', '100400', [
                'faultString' => "KEY: SESSID_".$this->context->device->serialNumber.'  SESSION ID: '. \Cache::get("SESSID_".$this->context->device->serialNumber)
            ]);
            return;
        }

        \Cache::put("SESSID_".$this->context->device->serialNumber, $this->context->session()->getId(),(5*60));

        $task = new Task(Types::INFORMResponse);
        $this->context->tasks->addTask($task);
        $this->context->provision->queueTasks();

        if($this->context->provisioningCurrentState === Context::PROVISIONING_STATE_INFORM && $this->context->new === true) {
            $this->context->provisioningCurrentState = Context::PROVISIONING_STATE_READPARAMS;
        }

    }

    private function updateDeviceData(): void {
        if(Device::whereSerialNumber($this->context->device->serialNumber)->exists() === false) {
            $this->context->new = true;
        }

        $this->context->deviceModel = Device::firstOrNew(
            [
                'serial_number' => $this->context->device->serialNumber,
            ]
        );

        $this->context->deviceModel->fill([
            'software_version' => $this->context->parameterValues->get($this->context->device->root .'DeviceInfo.SoftwareVersion')?->value,
            'product_class' => $this->context->device->productClass,
            'oui' => $this->context->device->oui,
            'connection_request_url' => $this->context->parameterValues->get($this->context->device->root . "ManagementServer.ConnectionRequestURL")->value,
            'updated_at' => now(),
        ]);

        if($this->context->deviceModel->exists === false) {
            $this->context->deviceModel->debug = (bool) Setting::getValue('debug_new_devices');
        }

        $this->context->deviceModel->save();

//        $this->context->device->new = $this->context->deviceModel->wasRecentlyCreated;
    }

    private function sessionExists(): bool {
        return false;
        return \Cache::has("SESSID_".$this->context->device->serialNumber);
    }
}
