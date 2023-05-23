<?php
declare(strict_types=1);

namespace App\ACS\Logic\Script;

use Illuminate\Support\Collection;

class Stack
{
    public const COMMAND_SPV = 'SPV';
    public const COMMAND_GPV = 'GPV';
    public const COMMAND_ADDOBJ = 'ADDOBJ';
    public const COMMAND_DELOBJ = 'DELOBJ';
    public const COMMAND_COMMIT = 'COMMIT';

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

    public function queueTasks() {

    }

    public function shift(): array {
        return $this->commands->shift();
    }

    public function reset(): void {
        $this->commands = new Collection();
    }
}
