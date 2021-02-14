<?php

declare(strict_types=1);


namespace App\ACS\Response;


use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\XML\BoolConverter;
use App\ACS\XML\ParameterListReader;
use Illuminate\Support\Collection;

class SetParameterValuesResponse extends CPEResponse
{
    private int $status;


    public function __construct(\DOMNode $body) {
        parent::__construct($body);
        $this->readValues();
    }

    private function readValues()
    {
        $xpath = new \DOMXPath($this->body->ownerDocument);
        /** @var \DOMElement $statusElement */
        $statusElement = $xpath->query("//cwmp:SetParameterValuesResponse/Status")->item(0);
        $this->status = (int) $statusElement->nodeValue;
    }

    /**
     * @return string
     */
    public function getStatus(): int
    {
        return $this->status;
    }


}
