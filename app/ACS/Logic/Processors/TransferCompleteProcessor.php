<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Response\TransferCompleteResponse;

class TransferCompleteProcessor extends Processor
{

    public function __invoke()
    {
        $this->context->provision->queueTasks();
        $this->context->acsResponse = new TransferCompleteResponse($this->context);
    }
}
