<?php

declare(strict_types=1);


namespace App\ACS\Response;


use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\XML\BoolConverter;
use App\ACS\XML\ParameterListReader;
use Illuminate\Support\Collection;

class AddObjectResponse extends CPEResponse
{
    private \DOMNode $body;
    private int $status;
    protected int $instanceNumber;

    public function __construct(\DOMNode $body) {
        $this->body = $body;
        $this->readValues();
    }

    private function readValues()
    {
        $xpath = new \DOMXPath($this->body->ownerDocument);
        /** @var \DOMElement $statusElement */
        $instanceElement = $xpath->query("//cwmp:AddObjectResponse/InstanceNumber")->item(0);
        $statusElement = $xpath->query("//cwmp:AddObjectResponse/Status")->item(0);
        $this->instanceNumber = (int) $instanceElement->nodeValue;
        $this->status = (int) $statusElement->nodeValue;
    }

    /**
     * @return int
     */
    public function getInstanceNumber(): int
    {
        return $this->instanceNumber;
    }


    /**
     * @return string
     */
    public function getStatus(): int
    {
        return $this->status;
    }


}
