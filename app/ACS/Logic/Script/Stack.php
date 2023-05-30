<?php
declare(strict_types=1);

namespace App\ACS\Logic\Script;

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
        foreach($this->commands as $command) {

        }
    }

    public function shift(): array {
        return $this->commands->shift();
    }

    public function reset(): void {
        $this->commands = new Collection();
    }
}
