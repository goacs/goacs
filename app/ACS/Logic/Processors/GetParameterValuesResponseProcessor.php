<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Context;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Entities\Task;
use App\ACS\Events\ParameterLookupDone;
use App\ACS\Logic\DeviceParametersLogic;
use App\ACS\Logic\TaskRunner;
use App\ACS\Types;
use App\Models\DeviceParameter;
use App\Models\Setting;
use Illuminate\Contracts\Events\Dispatcher;

class GetParameterValuesResponseProcessor extends Processor
{
    private Dispatcher $dispatcher;

    public function __construct(Context $context, Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        parent::__construct($context);
    }

    public function __invoke()
    {
        $rb = Setting::getValue('read_behaviour');
        $flag = $rb === 'boot';
        if($this->context->new || $rb === 'boot') {
            //Save Parameters in db
            DeviceParameter::massUpdateOrInsert($this->context->deviceModel, $this->context->cpeResponse->parameters, $flag);

            if($this->context->tasks->isNextTask(Types::GetParameterValues) === false) {
                //Save object parameters
                DeviceParameter::massUpdateOrInsert(
                    $this->context->deviceModel,
                    $this->context->parameterInfos->filterEndsWithDot()->toParameterValuesCollecton(),
                    $flag
                );
            }
        }
        /*else if($rb === 'boot') {
            DeviceParameter::massUpdateOrInsert(
                $this->context->deviceModel,
                $this->context->cpeResponse->parameters,
                true
            );
        }  */

        if(\Arr::get($this->context->tasks->prevTask()?->payload ?? [], 'store', false) === true) {
            DeviceParameter::massUpdateOrInsert(
                $this->context->deviceModel,
                $this->context->cpeResponse->parameters
            );
        }

        if($this->context->tasks->isNextTask(Types::GetParameterValues) === false) {
            if($this->context->lookupParameters === true) {
                \Cache::put(
                    Context::LOOKUP_PARAMS_PREFIX.$this->context->device->serialNumber,
                    $this->context->parameterValues,
                    now()->addMinutes((int)Setting::getValue('lookup_cache_ttl'))
                );
                $this->dispatcher->dispatch(new ParameterLookupDone($this->context->deviceModel, $this->context->parameterValues));
                return;
            }

//            if($this->context->provisioningCurrentState === Context::PROVISIONING_STATE_PROCESSING) {
                $root = $this->context->device->root;
                $settingsUsername = Setting::getValue('connection_request_username');
                $settingsPassword = Setting::getValue('connection_request_password');
                DeviceParameter::setParameter($this->context->deviceModel->id, $root.'ManagementServer.ConnectionRequestUsername', $settingsUsername, 'RWS', 'xsd:string');
                DeviceParameter::setParameter($this->context->deviceModel->id, $root.'ManagementServer.ConnectionRequestPassword', $settingsPassword, 'RWS', 'xsd:string');

//                $this->loadGlobalTasks(Types::GetParameterValuesResponse);

                $this->context->provision->queueTasks();

                $this->context->tasks->addTask(new Task(Types::SetParameterValuesProcessor));
//                (new SetParameterValuesRequestProcessor($this->context))();
//            }
        }
    }


}
