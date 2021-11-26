<?php

declare(strict_types=1);


namespace App\ACS\Request;


abstract class CPERequest
{
    protected \DOMNode $body;
    public string $commandKey = '';

    public function __construct(\DOMNode $body)
    {
        $this->body = $body;
    }
}
