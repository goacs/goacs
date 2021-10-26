<?php


namespace App\ACS\Logic\Script;


use App\ACS\Context;
use App\ACS\Entities\Device;
use App\ACS\Entities\Task;
use App\ACS\Types;
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

    public function addObject(string $path) {
        $task = new Task(Types::AddObject);
        $task->setPayload(['parameter' => $path]);
        $this->context->tasks->addTask($task);
    }

    public function deleteObject(string $path) {
        $task = new Task(Types::DeleteObject);
        $task->setPayload(['parameter' => $path]);
        $this->context->tasks->addTask($task);
    }

    public function uploadFirmware(string $filename) {
        $task = $this->deviceModel->tasks()->make();
        $task->name = Types::Download;
        $task->on_request = Types::EMPTY;
        $task->not_before = now()->subDay();
        $task->infinite = false;
        $task->payload = [
            'filetype' => '1 Firmware Upgrade Image',
            'filename' => $filename
        ];
        $task->save();
    }

    public function reboot() {
        $task = new Task(Types::Reboot);
        $this->context->tasks->addTask($task);
    }

    public function factoryReset() {
        $task = new Task(Types::FactoryReset);
        $this->context->tasks->addTask($task);
    }
}
