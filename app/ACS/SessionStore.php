<?php

declare(strict_types=1);


namespace App\ACS;

//TODO
class SessionStore
{
    private string $sessionId = '';

    public function __construct(Context $context)
    {
        $this->sessionId = $context->session()->getId();
    }

    public function store() {

    }

    public function restore() {

    }
}
