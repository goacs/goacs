<?php


namespace App\ACS\Entities;


use Illuminate\Support\Collection;

class TaskCollection extends Collection
{
    public function hasTaskOfType(string $type): bool {
        return $this->filter(fn(Task $task) => $task->name === $type)->count() > 0;
    }

    public function isNextTask(string $type): bool {
        /** @var Task $task */
        $task = $this->first();
        if($task === null) {
            return false;
        }

        return $task->name === $type;
    }

    public function addTask(Task $task) {
        $this->push($task);
    }

    public function nextTask(): ?Task {
        return $this->filter(fn(Task $task) => $task->done_at === null)->first();
    }

    public function prevTask(): ?Task {
        return $this->filter(fn(Task $task) => $task->done_at !== null)->last();
    }
}
