<?php

declare(strict_types=1);


namespace App\ACS\Entities;


class Task
{
    public string $name;
    public array $payload;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function setPayload(array $payload) {
        $this->payload = $payload;
    }
}
