<?php

declare(strict_types=1);


namespace App\ACS\Response;


class TransferCompleteResponse extends ACSResponse
{
    public function getBody(): string
    {
        return '<cwmp:TransferCompleteResponse/';
    }
}
