<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\ParameterInfoCollection;
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

        $gpnTask = new Task(Types::GetParameterNames);
        $gpnTask->setPayload([
            'parameter' => rtrim($path, ".").".".$addObjectResponse->getInstanceNumber()."."
        ]);
        $gpvTask = new Task(Types::GetParameterValues);
        $gpvTask->setPayload(
            [
                'parameters' => new ParameterInfoCollection([
                    (new ParameterInfoStruct())->setName(rtrim($path, ".").".".$addObjectResponse->getInstanceNumber().".")
                ]),
                'store' => true,
            ]
        );

        $this->context->tasks->putAsNextTask($gpvTask);
        $this->context->tasks->putAsNextTask($gpnTask);

    }
}
