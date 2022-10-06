<?php


namespace App\ACS\Entities;


use Illuminate\Support\Collection;

class TaskCollection extends Collection
{
    public function hasTaskOfType(string $type): bool {
        return $this->filter(fn(Task $task) => $task->name === $type && $task->done_at === null)->count() > 0;
    }

    public function getTaskOfType(string $type): ?Task {
        return $this->filter(fn(Task $task) => $task->name === $type && $task->done_at === null)->first();
    }

    public function isNextTask(string $type): bool {
        /** @var Task $task */
        $task = $this->nextTask();
        if($task === null) {
            return false;
        }

        return $task->name === $type;
    }

    public function addTask(Task $task) {
        $this->push($task);
    }

    public function putAsNextTask(Task $task) {
        $foundIndex = $this->getNextTaskIndex();
        $this->splice($foundIndex, 0, [$task]);
    }

    public function addTaskBeforeTask(Task $task, string $before) {
        $index = $this->search(fn(Task $othTask) => $othTask->name === $before && $othTask->done_at === null);

        if($index !== false) {
            $this->splice($index, 0 ,[$task]);
            return;
        }

        $this->add($task);
    }



    public function nextTask(): ?Task {
        return $this->filter(fn(Task $task) => $task->done_at === null)->first();
    }

    public function prevTask(): ?Task {
        return $this->filter(fn(Task $task) => $task->done_at !== null)->last();
    }

    public function hasTasksToRun(): bool {
        return $this->filter(fn(Task $task) => $task->done_at === null)->count() > 0;
    }

    public function flush() {
        $this->items  = [];
    }

    private function getNextTaskIndex(): int|bool {
        return $this->search(function(Task $task, $key) {
            return $task->done_at === null;
        });
    }
}
