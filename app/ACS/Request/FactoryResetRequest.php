<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Context;

class FactoryResetRequest extends ACSRequest
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function getBody(): string
    {
        $body = '<cwmp:FactoryReset></cwmp:FactoryReset>';

        return $this->withBaseBody($body);
    }
}
