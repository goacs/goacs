<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Types;

class SetParameterValuesResponseProcessor extends Processor
{

    public function __invoke()
    {
        $prevTask = $this->context->tasks->prevTask();
        if($prevTask !== null && $prevTask->name === Types::SetParameterValues) {
            $parameters = $prevTask->payload['parameters'];
            /** @var ParameterValueStruct $parameter */
            foreach ($parameters as $parameter) {
                $this->context->parameterValues->put($parameter->name, $parameter);
            }
        }
    }
}
