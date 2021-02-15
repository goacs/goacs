<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Context;

abstract class ACSRequest
{
    /**
     * @var Context
     */
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public abstract function getBody(): string;

    protected function withBaseBody(string $body): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cwmp="urn:dslforum-org:cwmp-1-0" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <soapenv:Header>
        <cwmp:ID soapenv:mustUnderstand="1">' . $this->context->envelopeId() . '</cwmp:ID>
        </soapenv:Header>
        <soapenv:Body>' . $body . '</soapenv:Body>
        </soapenv:Envelope>';
    }
}
