<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Context;
use App\ACS\Entities\Tasks\GetParameterNamesTask;
use App\ACS\Entities\Tasks\Task;
use App\ACS\Types;

class EmptyResponseProcessor extends Processor
{

    public function __invoke()
    {
        $this->context->provision->queueTasks();

        if($this->context->tasks->nextTask() !== null && $this->context->tasks->nextTask()->isOnRequest(Types::EMPTY) === false) {
            return;
        }

//        if($this->context->new === true || $this->context->provision === true || $this->context->lookupParameters) {
        if($this->context->provisioningCurrentState === Context::PROVISIONING_STATE_READPARAMS) {
            $task = new GetParameterNamesTask();
            $task->setPayload([
                'parameters' => $this->context->device->root
            ]);

            $this->context->tasks->addTask($task);
        }
    }
}
