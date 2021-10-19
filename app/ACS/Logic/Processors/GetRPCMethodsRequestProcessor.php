<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Response\GetRPCMethodsACSResponse;

class GetRPCMethodsRequestProcessor extends Processor
{

    public function __invoke()
    {
        $this->context->acsResponse = new GetRPCMethodsACSResponse($this->context);
    }
}
