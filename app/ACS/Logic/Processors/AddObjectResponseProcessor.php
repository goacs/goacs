<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\ParameterInfoCollection;
use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\Tasks\GetParameterNamesTask;
use App\ACS\Entities\Tasks\GetParameterValuesTask;
use App\ACS\Entities\Tasks\Task;
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
        $path = $prevTask->payload['parameters'];

        $gpnTask = new GetParameterNamesTask();
        $gpnTask->setPayload([
            'parameter' => rtrim($path, ".").".".$addObjectResponse->getInstanceNumber()."."
        ]);
        $gpvTask = new GetParameterValuesTask();
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
