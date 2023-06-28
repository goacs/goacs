<?php


namespace App\ACS\Logic\Script;


use App\ACS\Context;
use App\ACS\Entities\Device;
use App\ACS\Entities\Flag;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Entities\Tasks\FactoryResetTask;
use App\ACS\Entities\Tasks\RebootTask;
use App\ACS\Entities\Tasks\Task;
use App\ACS\Types;
use App\Models\Device as DeviceModel;
use App\Models\DeviceParameter;
use App\Models\Template;
use Illuminate\Support\Str;
use function PHPUnit\Framework\containsIdentical;

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
    private Sandbox $sandbox;

    public function __construct(Context $context) {

        $this->context = $context;
        $this->device = $this->context->device;
        $this->deviceModel = $this->context->deviceModel;
        $this->deviceModel->load('parameters');
    }

    public function setParameter($path, $value, $flag = 'RWS', $type = 'xsd:string') {
        $flag = Flag::fromString($flag);

        DeviceParameter::setParameter($this->deviceModel->id, $path, $value, $flag, $type);

        if($flag->system) {
            return;
        }

        $this->context->getScriptStack()->add(Types::SetParameterValues, [
            'parameters' => ParameterValuesCollection::fromArray([[
                'name' => $path,
                'value' => (string) $value,
                'flag' => $flag,
                'type' => $type,
            ]]),
        ]);
    }

    public function getParameterValue($path) {
//        $this->context->getScriptStack()->add(Types::GetParameterValues, [
//            'parameters' => ParameterValuesCollection::fromArray([[
//                'name' => $path,
//                'value' => '',
//                'flag' => new Flag(),
//                'type' => 'xsd:string',
//            ]])
//        ]);
        return DeviceParameter::getParameterValue($this->deviceModel->id, $path);
    }

    public function commit() {
        $this->context->getScriptStack()->add(Types::Commit, []);
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
        $this->context->getScriptStack()->add(Types::AddObject, [
            'parameters' => $path
        ]);
//        $task = new Task(Types::AddObject);
//        $task->setPayload(['parameter' => $path]);
//        $this->context->tasks->addTask($task);
    }

    public function deleteObject(string $path) {
        $this->context->getScriptStack()->add(Types::DeleteObject, [
            'parameters' => $path
        ]);
//        $task = new Task(Types::DeleteObject);
//        $task->setPayload(['parameter' => $path]);
//        $this->context->tasks->addTask($task);
    }

    public function uploadFirmware(string $filename) {
        $this->context->getScriptStack()->add(Types::Download,
            [
                'filetype' => '1 Firmware Upgrade Image',
                'filename' => $filename
            ]
        );
//        $task = $this->deviceModel->tasks()->make();
//        $task->name = Types::Download;
//        $task->on_request = '';
//        $task->not_before = now()->subDay();
//        $task->infinite = false;
//        $task->payload = [
//            'filetype' => '1 Firmware Upgrade Image',
//            'filename' => $filename
//        ];
//        $task->save();
    }

    public function reboot() {
        $task = new RebootTask();
        $this->context->tasks->addTask($task);
    }

    public function factoryReset() {
        $task = new FactoryResetTask();
        $this->context->tasks->addTask($task);
    }
}
