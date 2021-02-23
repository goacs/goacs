<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use Carbon\Carbon;

class Task
{
    public string $name;
    public string $onRequest = '';
    public array $payload = [];
    public ?Carbon $done_at = null;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function setPayload(array $payload) {
        $this->payload = $payload;
    }

    public function done() {
        $this->done_at = now();
    }
}
