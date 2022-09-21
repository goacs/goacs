<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\ParameterInfoCollection;
use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\Task;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Types;

class GetParameterNamesResponseProcessor extends Processor
{
    const GET_PARAMETER_VALUES_CHUNK_SIZE = 4;

    public function __invoke()
    {
        /** @var GetParameterNamesResponse $getParameterNamesResponse */
        $getParameterNamesResponse = $this->context->cpeResponse;

        if($this->context->tasks->isNextTask(Types::GetParameterNames) === false) {
            $filteredParameters = $this->filterDenyParameters($getParameterNamesResponse->parameters);
            $filteredParameters = $filteredParameters->filterByChunkCount(2,2)->filterEndsWithDot();

            if($filteredParameters->isEmpty()) {
                $filteredParameters = $this->filterDenyParameters($getParameterNamesResponse->parameters);
            }

            foreach ($filteredParameters->chunk(self::GET_PARAMETER_VALUES_CHUNK_SIZE) as $chunk) {
                $task = new Task(Types::GetParameterValues);
                $task->setPayload([
                    'parameters' => $chunk
                ]);
                $this->context->tasks->addTask($task);
            }
        }
    }

    private function filterDenyParameters(ParameterInfoCollection $chunk): ParameterInfoCollection
    {
        return $chunk->filter(function(ParameterInfoStruct $parameter) {
            foreach ($this->context->deniedParameters as $deniedParameter) {
//                dump('denied param', $deniedParameter);

                if(preg_match('/^'.$deniedParameter.'$/', $parameter->name)){
                    dump('passed', $deniedParameter);
                    return false;
                }
            }
            return true;
        });
    }
}
