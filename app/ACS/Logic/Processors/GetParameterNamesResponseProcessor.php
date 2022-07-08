<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\ParameterInfoCollection;
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
            $filteredParameters = $getParameterNamesResponse->parameters->filterByChunkCount(2,2)->filterEndsWithDot();
            foreach ($filteredParameters->chunk(self::GET_PARAMETER_VALUES_CHUNK_SIZE) as $chunk) {
                $chunk = $this->filterDenyParameters($chunk);
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
        $deniedParameters = $this->context->provision->getDeniedParameters();

        return $chunk->filter(function($parameter) use ($deniedParameters) {
            foreach ($deniedParameters as $deniedParameter) {
                if(preg_match('/^'.$deniedParameter->parameter.'$/', $parameter)){
                    return false;
                }
            }
            return true;
        });
    }
}
