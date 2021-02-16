<?php

declare(strict_types=1);


namespace App\ACS\Logic;


use App\ACS\Context;
use App\ACS\Entities\ParameterValuesCollection;

class ParameterSetterLogic
{
    /**
     * @var Context
     */
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getParametersToSend(): ParameterValuesCollection {
        if($this->context->boot === true)
        {
            return $this->getParametersForBoot();
        }
        return new ParameterValuesCollection();
    }

    public function getParametersForBoot(): ParameterValuesCollection {
        return new ParameterValuesCollection();

    }
}
