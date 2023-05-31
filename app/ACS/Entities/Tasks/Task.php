<?php

declare(strict_types=1);


namespace App\ACS\Entities\Tasks;


use App\ACS\Context;
use App\ACS\Request\ACSRequest;
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

    public function toRequest(Context $context): ?ACSRequest {
        return null;
    }

    public function isOnRequest(string $requestType) {
        if($this->onRequest === '') {
            return true;
        }

        return $this->onRequest === $requestType;
    }

}
