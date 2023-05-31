<?php

declare(strict_types=1);


namespace App\ACS\Logic\Script\Providers;


use App\ACS\Context;
use App\ACS\Entities\Device;
use App\ACS\Entities\Tasks\Task;
use App\ACS\Types;
use App\Models\Device as DeviceModel;
use App\Models\DeviceParameter;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class DeviceFunctionsProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var Device
     */
    private ?Device $device;

    /**
     * @var DeviceModel
     */
    private ?DeviceModel $deviceModel;

    public function __construct(private Context $context)
    {
        $this->device = $this->context->device;
        $this->deviceModel = $this->context->deviceModel;
        $this->deviceModel->load('parameters');
    }

    private function setFunction() {
        return new ExpressionFunction('set', function(/*$name, $value,  $flags = 'RWS', $type = null*/ ...$args) {
            return sprintf('\%s([%s, %s], %s)', 'call_user_func', DeviceParameter::class, 'setParemeter', implode(', ', $args));
        },
        function ($args, $name, $value, $flags = 'RWS', $type = null) {
            DeviceParameter::setParameter($this->deviceModel->id, $name, $value, $flags, $type);
        });
    }

    private function getFunction() {
        return new ExpressionFunction('get', function(/*$name, $value,  $flags = 'RWS', $type = null*/ ...$args) {
            return sprintf('\%s([%s, %s], %s)', 'call_user_func', DeviceParameter::class, 'getParameterValue', implode(', ', $args));
        },
        function ($args, $name) {
            DeviceParameter::getParameterValue($this->deviceModel->id, $name);
        });
    }

    private function addObjectFunction() {
        return new ExpressionFunction('addObject', function(/*$name, $value,  $flags = 'RWS', $type = null*/ $path) {
            return sprintf('\%s(%s)', 'addObject', $path);
        },
        function ($args, $path) {
            $task = new Task(Types::AddObject);
            $task->setPayload(['parameter' => $path]);
            $this->context->tasks->addTask($task);
        });
    }

    private function deleteObjectFunction() {
        return new ExpressionFunction('deleteObject', function(/*$name, $value,  $flags = 'RWS', $type = null*/ $path) {
            return sprintf('\%s(%s)', 'deleteObject', $path);
        },
            function ($args, $path) {
                $task = new Task(Types::DeleteObject);
                $task->setPayload(['parameter' => $path]);
                $this->context->tasks->addTask($task);
            });
    }

    private function rebootFunction() {
        return new ExpressionFunction('reboot', function(/*$name, $value,  $flags = 'RWS', $type = null*/ ...$args) {
            return sprintf('\%s()', 'reboot');
        },
        function ($args) {
            $task = new Task(Types::Reboot);
            $this->context->tasks->addTask($task);
        });
    }

    private function factoryFunction() {
        return new ExpressionFunction('factory', function(/*$name, $value,  $flags = 'RWS', $type = null*/ ...$args) {
            return sprintf('\%s()', 'factory');
        },
        function ($args) {
            $task = new Task(Types::FactoryReset);
            $this->context->tasks->addTask($task);
        });
    }

//    private function existFunction() {
//        return new ExpressionFunction('exist', function(/*$name, $value,  $flags = 'RWS', $type = null*/ ...$args) {
//            return sprintf('\%s([%s, %s], %s)', 'call_user_func', DeviceParameter::class, 'getParameterValue', implode(', ', $args));
//        },
//            function ($args, $name) {
//                DeviceParameter::getParameterValue($this->deviceModel->id, $name);
//            });
//    }

    public function getFunctions()
    {
        return [
            $this->setFunction(),
            $this->getFunction(),

        ];
    }
}
