<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Context;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;

class SetParameterValuesRequest extends ACSRequest
{
    /**
     * @var ParameterValuesCollection|ParameterValueStruct[]
     */
    private ParameterValuesCollection $parameterValuesCollection;

    public function __construct(Context $context, ParameterValuesCollection $parameterValuesCollection)
    {
        parent::__construct($context);
        $this->parameterValuesCollection = $parameterValuesCollection;
    }

    public function getBody(): string
    {
        $body = '<cwmp:SetParameterValues>
			<ParameterList soap-enc:arrayType="cwmp:ParameterValueStruct['.$this->parameterValuesCollection->count().']">';


        foreach ($this->parameterValuesCollection as $parameter) {
            $body .= '<ParameterValueStruct>';
            $body .= '<Name>'.$parameter->name.'</Name>';
            $body .= '<Value xsi:type="'.$parameter->type.'">'.$parameter->value.'</Value>';
            $body .= '</ParameterValueStruct>';
        }

        $body .= '</ParameterList>
          <ParameterKey></ParameterKey>
        </cwmp:SetParameterValues>';

        return $this->withBaseBody($body);

    }
}
