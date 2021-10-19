<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\Task;
use App\ACS\Response\AddObjectResponse;
use App\ACS\Types;
use Illuminate\Support\Collection;

class AddObjectResponseProcessor extends Processor
{

    public function __invoke()
    {

        $prevTask = $this->context->tasks->prevTask();
        if($prevTask->name !== Types::AddObject) {
            \Log::error('AddObjectResponse out of order!', ['context' => $this->context]);
            return;
        }
        /** @var AddObjectResponse $addObjectResponse */
        $addObjectResponse = $this->context->cpeResponse;
        $path = $prevTask->payload['parameter'];
        $gpvTask = new Task(Types::GetParameterValues);

        $gpvTask->setPayload(
            [
                'parameters' => new Collection([
                    (new ParameterInfoStruct())->name => $path.".".$addObjectResponse->getInstanceNumber()."."
                ]),
            ]
        );
    }
}
