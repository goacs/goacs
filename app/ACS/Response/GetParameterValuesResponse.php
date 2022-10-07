<?php

declare(strict_types=1);


namespace App\ACS\Response;


use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;

class GetParameterValuesResponse extends CPEResponse
{
    /**
     * @var ParameterValuesCollection
     */
    public ParameterValuesCollection $parameters;

    public function __construct(\DOMNode $body) {
        parent::__construct($body);
        $this->parameters = new ParameterValuesCollection();
        $this->readValues();
    }

    private function readValues()
    {
        $xpath = new \DOMXPath($this->body->ownerDocument);
        foreach ($xpath->query("//cwmp:GetParameterValuesResponse/ParameterList/*") as $parameterValueStruct)
        {
            $this->processList($parameterValueStruct);
        }
        unset($xpath);
    }

    private function processList(\DOMElement $element)
    {
        $parameterValueStruct = new ParameterValueStruct();
        foreach($element->childNodes as $childNode) {
            if($childNode instanceof \DOMElement) {
                switch ($childNode->nodeName) {
                    case 'Name':
                        $parameterValueStruct->name = $childNode->nodeValue;
                        break;
                    case 'Value':
                        $parameterValueStruct->type = $childNode->attributes->item(0)->nodeValue;
                        $parameterValueStruct->value = $childNode->nodeValue;
                        break;
                }
            }
        }
        if($parameterValueStruct->name !== '') {
            $this->parameters->put($parameterValueStruct->name, $parameterValueStruct);
        }
    }
}
