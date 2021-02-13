<?php

declare(strict_types=1);


namespace App\ACS\Response;


use App\ACS\Context;

abstract class ACSResponse
{
    /**
     * @var Context
     */
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    abstract public function getBody(): string;

    protected function withBaseBody(string $body): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <soap-env:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cwmp="urn:dslforum-org:cwmp-1-0" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <soap-env:Header>
        <cwmp:ID soap-env:mustUnderstand="1">' . $this->context->envelopeId() . '</cwmp:ID>
        </soap-env:Header>
        <soap-env:Body>' . $body . '</soap-env:Body>
        </soap-env:Envelope>';
    }
}
