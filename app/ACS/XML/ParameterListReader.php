<?php

declare(strict_types=1);


namespace App\ACS\XML;


use App\ACS\Entities\ParameterValueStruct;

class ParameterListReader
{
    private \DOMElement $parameterListElement;

    public function __construct(\DOMElement $parameterListElement) {

        $this->parameterListElement = $parameterListElement;
    }

    public function parameters(): array {
        $parameters = [];
        foreach ($this->parameterListElement->childNodes as $childNode) {
            if($childNode instanceof \DOMElement && $childNode->nodeName === "ParameterValueStruct") {
                $parameterValueStructObject = new ParameterValueStruct();
                foreach ($childNode->childNodes as $parameterValueStructElement) {
                    if(! $parameterValueStructElement instanceof \DOMElement) {
                        continue;
                    }
                    switch($parameterValueStructElement->nodeName) {
                        case 'Value':
                            if($parameterValueStructElement->hasChildNodes()) {
                                $parameterValueStructObject->value = $parameterValueStructElement->firstChild->nodeValue;
                            } else {
                                $parameterValueStructObject->value = $parameterValueStructElement->nodeValue;
                            }
                            if($parameterValueStructElement->attributes->item(0)) {
                                $parameterValueStructObject->type = $parameterValueStructElement->attributes->item(0)->firstChild->nodeValue;
                            }
                            break;
                        case 'Name':
                            $parameterValueStructObject->name = $parameterValueStructElement->firstChild->nodeValue;
                            break;
                    }
                }
                $parameters[] = $parameterValueStructObject;
            }
        }

        return $parameters;
    }
}
