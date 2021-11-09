<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\Task;
use App\ACS\Types;

class EmptyResponseProcessor extends Processor
{

    public function __invoke()
    {
        $this->loadGlobalTasks(Types::EMPTY);

        if($this->context->tasks->nextTask() !== null) {
            return;
        }

        if($this->context->new === true || $this->context->provision === true || $this->context->lookupParameters) {
            $task = new Task(Types::GetParameterNames);
            $task->setPayload([
                'parameter' => $this->context->device->root
            ]);

            $this->context->tasks->addTask($task);
        }
    }
}
