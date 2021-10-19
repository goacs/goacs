<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Types;

class DeleteObjectResponseProcessor extends Processor
{

    public function __invoke()
    {
        $prevTask = $this->context->tasks->prevTask();
        if($prevTask->name !== Types::DeleteObject) {
            \Log::error('DeleteObjectResponse out of order!', ['context' => $this->context]);
            return;
        }
        $path = $prevTask->payload['parameter'];
        $this->context->deviceModel->parameters()->pathset($path)->delete();
    }
}
