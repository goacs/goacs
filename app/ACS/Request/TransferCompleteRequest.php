<?php

declare(strict_types=1);


namespace App\ACS\Request;


class TransferCompleteRequest extends CPERequest
{
    public function __construct(\DOMNode $body)
    {
        parent::__construct($body);
        $this->readValues();
    }

    public function readValues() {

    }
}
