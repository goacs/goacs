<?php

declare(strict_types=1);


namespace App\ACS\Response;


use App\ACS\Entities\ParameterInfoCollection;
use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\XML\BoolConverter;
use Illuminate\Support\Collection;

class GetParameterNamesResponse extends CPEResponse
{
    /**
     * @var Collection
     */
    public ParameterInfoCollection $parameters;

    public function __construct(\DOMNode $body) {
        parent::__construct($body);
        $this->parameters = new ParameterInfoCollection();
        $this->readValues();
    }

    private function readValues()
    {
        $xpath = new \DOMXPath($this->body->ownerDocument);
        foreach ($xpath->query("//cwmp:GetParameterNamesResponse/ParameterList/*") as $paramterInfoStruct)
        {
           $this->processList($paramterInfoStruct);
        }
    }

    private function processList(\DOMElement $element)
    {
        $parameterInfoStruct = new ParameterInfoStruct();
        foreach($element->childNodes as $childNode) {
            if($childNode instanceof \DOMElement) {
                switch ($childNode->nodeName) {
                    case 'Name':
                        $parameterInfoStruct->setName(trim($childNode->nodeValue));
                        break;
                    case 'Writable':
                        $parameterInfoStruct->writable = BoolConverter::stringToBool($childNode->nodeValue);
                        break;
                }
            }

        }
        if($parameterInfoStruct->name !== '') {
            $this->parameters->put($parameterInfoStruct->name, $parameterInfoStruct);
        }
    }
}
