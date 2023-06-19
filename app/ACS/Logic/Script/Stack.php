<?php
declare(strict_types=1);

namespace App\ACS\Logic\Script;

use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\Tasks\CommitTask;
use App\ACS\Entities\Tasks\Task;
use App\ACS\Types;
use Illuminate\Support\Collection;

class Stack
{
    private Collection $commands;

    public function __construct()
    {
        $this->commands = new Collection();
    }

    public function add(string $command, array $payload): void {
        $this->commands[] = [
            'command' => $command,
            'payload' => $payload,
        ];
    }

    public function groupByTaskType() {
        $tasks = [];
        $currentTask = new CommitTask();
        $this->add(Types::Commit, []);
        foreach($this->commands as $command) {
            if($command['command'] !== $currentTask->name) {
                $tasks[] = $currentTask;
                $currentTask = Task::fromType($command['command']);
                $currentTask->setPayload($command['payload']);
            } else {
                $currentTask->addParams($command['payload']['parameters'] ?? new ParameterValuesCollection());
            }

        }
        array_shift($tasks);

        return $tasks;
    }

    public function shift(): array {
        return $this->commands->shift();
    }

    public function reset(): void {
        $this->commands = new Collection();
    }
}
