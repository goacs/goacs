<?php


namespace App\ACS\Logic;


use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\Models\Device;

class DeviceParametersLogic
{
    /**
     * @var Device
     */
    private Device $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function combinedDeviceParametersWithTemplates() {
        $deviceParameters = ParameterValuesCollection::fromEloquent($this->device->parameters()->get());
        $templatesParameters = ParameterValuesCollection::fromEloquent($this->device->getTemplatesParameters());
        return $deviceParameters->merge($templatesParameters);
    }

    public function getParametersToCreateInstance(ParameterValuesCollection $sessionParameters, ParameterValuesCollection $dbParameters) {
        $diff = $dbParameters->diff($sessionParameters)->filterByFlag('object')->filterInstances();
        $toAddParams = new ParameterValuesCollection();
        /** @var ParameterValueStruct $parameter */
        foreach($diff as $parameter) {
            $chunks = array_filter(explode('.', $parameter->name));
            $instanceNumber = (int) $chunks[count($chunks) - 1];
            array_pop($chunks);
            $newName = join('.', $chunks).'.';
            for($i = $instanceNumber; $i >= 1; $i--) {
                if($sessionParameters->has($newName.$i.'.') === false) {
                    $parameter->name = $newName;
                    $parameter->type = 'object';
                    $toAddParams->add($parameter);
                    $sessionParameters->put($newName.$i.'.', $parameter);
                }
            }
        }

        return $toAddParams;
    }
}
