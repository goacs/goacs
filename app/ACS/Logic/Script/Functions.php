<?php


namespace App\ACS\Logic\Script;


use App\ACS\Context;
use App\ACS\Entities\Device;
use App\Models\Device as DeviceModel;
use App\Models\DeviceParameter;

class Functions
{
    /**
     * @var Context
     */
    private Context $context;
    /**
     * @var Device
     */
    private ?Device $device;

    /**
     * @var DeviceModel
     */
    private ?DeviceModel $deviceModel;

    public function __construct(Context $context) {

        $this->context = $context;
        $this->device = $this->context->device;
        $this->deviceModel = $this->context->deviceModel;
        $this->deviceModel->load('parameters');
    }

    public function setParameter($path, $value, $flags = 'RWS', $type = null) {
        DeviceParameter::setParameter($path, $value, $flags, $type);
    }

    public function getParameterValue($path) {
        return DeviceParameter::getParameterValue($path);
    }
}
