<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Context;

class RebootRequest extends ACSRequest
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function getBody(): string
    {
        $envelopeId = $this->context->envelopeId();
        $body = '<cwmp:Reboot>';
        $body .= '<CommandKey>'.$envelopeId.'</CommandKey>';
        $body .= '</cwmp:Reboot>';

        return $this->withBaseBody($body);
    }
}
