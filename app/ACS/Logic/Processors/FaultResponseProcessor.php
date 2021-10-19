<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Response\FaultResponse;
use App\Models\Log;

class FaultResponseProcessor extends Processor
{

    public function __invoke()
    {
        /** @var FaultResponse $response */
        $response = $this->context->cpeResponse;
        Log::fromFaultResponse($this->context->deviceModel, $response);
    }
}
