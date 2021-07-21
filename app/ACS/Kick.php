<?php


namespace App\ACS;


use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Kick
{
    const HTTPIE_EXECUTABLE = 'http';
    const PROCESS_TIMEOUT = 2;

    protected array $args = [];
    private string $url;
    private string $username;
    private string $password;

    public function __construct(string $url, string $username, string $password)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->checkForHttpie();
    }

    public function kick(): bool {
        if($this->kickUsingBasicAuth() === false) {
            return $this->kickUsingDigestAuth();
        }

        return true;
    }

    public function kickUsingDigestAuth(): bool {
        $args = ['-A', 'digest', ...$this->argBase(), $this->url];
        return $this->execProcess($args);
    }

    public function kickUsingBasicAuth(): bool {
        $args = [...$this->argBase(), $this->url];
        return $this->execProcess($args);
    }

    private function execProcess(array $args): bool {
        $process = new Process([self::HTTPIE_EXECUTABLE, ...$args]);
        dump($process->getCommandLine());
        $process->enableOutput();
        $process->setTimeout(5);
        $result = $process->run();
        dump($process->getOutput());
        dump($process->getErrorOutput());
        return $result === 0;
    }

    private function argBase(): array {
        return [
            '-a',
            "{$this->username}:{$this->password}",
            '--check-status',
            '--timeout',
            self::PROCESS_TIMEOUT,
            '--pretty',
            'none',
            'GET'
        ];
    }

    private function checkForHttpie()
    {
        $finder = new ExecutableFinder();
        $result = $finder->find(self::HTTPIE_EXECUTABLE);

        if($result === null) {
            throw new \RuntimeException('Cannot find HTTPie executable - check https://httpie.io');
        }
    }
}
