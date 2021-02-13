<?php

declare(strict_types=1);


namespace App\ACS\Response;


class FaultResponse extends ACSResponse
{

    public function getBody(): string
    {
        return "";
    }
}
