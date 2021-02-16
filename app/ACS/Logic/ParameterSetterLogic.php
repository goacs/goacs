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
        return new ParameterValuesCollection();
    }
}
