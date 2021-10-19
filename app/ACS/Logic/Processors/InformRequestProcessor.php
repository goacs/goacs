<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\Task;
use App\ACS\Request\InformRequest;
use App\ACS\Types;
use App\Models\Device;

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
//        $this->context->device->new = $this->context->deviceModel->wasRecentlyCreated;
    }
}
