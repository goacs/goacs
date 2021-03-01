<?php


namespace App\ACS\Logic\Script;


use App\ACS\Context;
use App\ACS\Entities\Device;
use App\Models\Device as DeviceModel;
use App\Models\DeviceParameter;
use App\Models\Template;

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
        DeviceParameter::setParameter($this->deviceModel->id, $path, $value, $flags, $type);
    }

    public function getParameterValue($path) {
        return DeviceParameter::getParameterValue($this->deviceModel->id, $path);
    }

    public function parameterExist($path) {
        return DeviceParameter::where(['device_id' => $this->deviceModel->id, 'name' => $path])->exists();
    }

    public function assignTemplateByName($name, int $priority = 100) {
        $template = Template::where('name', $name)->first();

        if($template !== null) {
            $this->assignTemplateById($template->id, $priority);
        }
    }

    public function assignTemplateById($id, int $priority = 100) {
        if($this->deviceModel->templates()->find($id) === null) {
            $this->deviceModel->templates()->attach($id, ['priority' => $priority]);
        }
    }
}
