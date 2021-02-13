<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Entities\ParameterValueStruct;
use Illuminate\Support\Collection;

class GetParameterValuesRequest extends ACSRequest
{
    /**
     * @var ParameterValueStruct[]|Collection
     */
    public Collection $parameters;

    public function setParameters(Collection $parameters) {
        $this->parameters = $parameters;
    }

    public function getBody(): string
    {
        $body = "";

        if(!$this->parameters) {
            return $body;
        }

        $body .= '<cwmp:GetParameterValues><ParameterNames soap:arrayType="xsd:string['.(string)$this->parameters->count().']">';
        foreach ($this->parameters as $parameter) {
            $body .= '<string>'.$parameter->name.'</string>';
        }

        $body .= '</ParameterNames></cwmp:GetParameterValues>';

        return $this->withBaseBody($body);
    }
}
