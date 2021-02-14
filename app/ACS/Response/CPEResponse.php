<?php

declare(strict_types=1);


namespace App\ACS\Response;


abstract class CPEResponse
{
    protected \DOMNode $body;

    public function __construct(\DOMNode $body)
    {
        $this->body = $body;
    }
}
