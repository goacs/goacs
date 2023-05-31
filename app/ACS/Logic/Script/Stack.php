<?php
declare(strict_types=1);

namespace App\ACS\Logic\Script;

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

    public function add(string $command, array $params): void {
        $this->commands[] = [
            'command' => $command,
            'params' => $params,
        ];
    }

    public function groupByTaskType() {
        $tasks = [];
        $currentTask = new Task(Types::Commit);
        foreach($this->commands as $command) {
            if($command['command'] !== $currentTask->name) {
                $currentTask = new Task($command['command']);
                $currentTask->setPayload($command['params']);
            }
        }
    }

    public function shift(): array {
        return $this->commands->shift();
    }

    public function reset(): void {
        $this->commands = new Collection();
    }
}
