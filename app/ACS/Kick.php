<?php


namespace App\ACS;


use App\Models\Device;
use App\Models\Setting;
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

    public static function fromDevice(Device $device) {
        if($device->connection_request_password !== null && $device->connection_request_password !== '') {
            $auth = [$device->connection_request_user, $device->connection_request_password];
        } else {
            $auth = [Setting::getValue('connection_request_username'), Setting::getValue('connection_request_password')];
        }

        return new Kick($device->connection_request_url, $auth[0], $auth[1]);
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
        $process->enableOutput();
        $process->setTimeout(5);
        $result = $process->run();
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
