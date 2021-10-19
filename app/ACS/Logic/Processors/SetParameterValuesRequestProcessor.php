<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Entities\Task;
use App\ACS\Logic\DeviceParametersLogic;
use App\ACS\Types;
use App\Models\DeviceParameter;
use App\Models\Setting;

class SetParameterValuesRequestProcessor extends Processor
{
    const SET_PARAMETER_VALUES_CHUNK_SIZE = 10;

    public function __invoke()
    {
        $this->setPII();
        $this->compareAndProcessObjectParameters();
        $this->compareAndProcessSetParameters();
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

    private function setPII()
    {
        if($pvs = $this->context->parameterValues->get($this->context->device->root.'ManagementServer.PeriodicInformInterval')) {
            $pvs->value = (string)$this->calculatePIIValue();
            DeviceParameter::setParameter($this->context->deviceModel->id, $pvs->name, $pvs->value);
        }
    }

    private function calculatePIIValue(): int {
        if(($spread = Setting::getValue('pii')) !== '')
        {
            [$min, $max] = explode('-', $spread);
        }
        return rand((int)$min, (int)$max);
    }
}