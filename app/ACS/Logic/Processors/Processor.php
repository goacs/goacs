<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Context;

abstract class Processor
{
    public function __construct(protected Context $context) {}

    abstract public function __invoke();

    protected function loadGlobalTasks(string $on_request)
    {
        $tasks = \App\Models\Task::where([
            'for_type' => \App\Models\Task::TYPE_GLOBAL,
            'on_request' => $on_request
        ])->get();

        foreach ($tasks as $task) {
            $this->context->tasks->addTask($task->toACSTask());
            if($task->infinite === false) {
                $task->delete();
            }
        }
    }
}
