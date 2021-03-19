<?php

declare(strict_types=1);


namespace App\ACS\Response;


class InformResponse extends ACSResponse
{

    public function getBody(): string
    {
        return $this->withBaseBody("<cwmp:InformResponse><MaxEnvelopes>1</MaxEnvelopes></cwmp:InformResponse>");
    }
}
