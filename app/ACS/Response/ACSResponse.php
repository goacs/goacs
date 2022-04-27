<?php

declare(strict_types=1);


namespace App\ACS\Response;


use App\ACS\Context;

abstract class ACSResponse
{
    /**
     * @var Context
     */
    protected Context $context;
    public string $type;

    public function __construct(Context $context, string $type = 'xml')
    {
        $this->context = $context;
        $this->type = $type;
    }

    abstract public function getBody(): string;

    protected function withBaseBody(string $body): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<soap-env:Envelope xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:cwmp="'.$this->context->cwmpUri.'">
<soap-env:Header><cwmp:ID soap-env:mustUnderstand="1">' . $this->context->envelopeId() . '</cwmp:ID></soap-env:Header><soap-env:Body>' . $body . '</soap-env:Body></soap-env:Envelope>';
    }

    public function getBaseName(): string {
        $class_parts = explode('\\', get_class($this));
        return end($class_parts) ?? '';
    }
}
