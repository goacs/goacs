<?php

namespace App\ACS\Logic;

use App\ACS\Context;
use App\ACS\Entities\Event;
use App\ACS\Entities\Task;
use App\ACS\Types;
use App\Models\DeviceParameter;
use App\Models\Log;
use App\Models\Provision as ProvisionModel;
use App\Models\ProvisionRule;
use Illuminate\Support\Collection;

class Provision
{
    public array $passedProvisions = [];

    public function __construct(private Context $context) {}

    public function queueTasks() {
        /** @var ProvisionModel $provision */
        foreach($this->getProvisions() as $provision) {
            Log::logInfo($this->context, 'Passed provision: '.$provision->name);
            $task = new Task(Types::RunScript);
            $task->setPayload(['script' => $provision->script]);
            $this->context->tasks->addTask($task);
        }
    }

    public function getDeniedParameters(): Collection {
        $parameters = new Collection();
        foreach ($this->getProvisions(skipEvents: true, skipRequests: true, disableCache: true) as $provision) {
            foreach($provision->denied as $deniedParameter) {
                $parameter = str_replace('$root.', $this->context->device->root, $deniedParameter->parameter);
                $parameters[] = $parameter;
            }
        }

        $this->passedProvisions = [];

        return $parameters->unique()->values();
    }

    public function getProvisions($force = false, $skipEvents = false, $skipRequests = false, $disableCache = false): array {
        if($force === false && count($this->passedProvisions) > 0) {
            return $this->passedProvisions;
        }

        /** @var ProvisionModel[] $storedProvisions */
        $storedProvisions = ProvisionModel::with(['rules','denied'])->orderByRaw('name, length(name)')->get();
        $passedProvisions = [];

        $this->passedProvisions = [];
        foreach ($storedProvisions as $storedProvision) {
//            dump('Checking provision: '. $storedProvision->name);
            //Filter Events
            $requestEvents = $this->context->events->map(fn(Event $item) => $item->getCode())->values()->toArray();
            if (!$skipEvents && $storedProvision->events !== '' && count(array_intersect($storedProvision->eventsArray(), $requestEvents)) === 0) {
                continue;
            }

            //Filter Request
            if(!$skipRequests && $storedProvision->requests !== '' && in_array($this->context->bodyType, $storedProvision->requestsArray()) === false) {
                continue;
            }

            //Filter Rules
            if($storedProvision->rules->isNotEmpty() && $this->evaluateRules($storedProvision->rules) === false) {
                continue;
            }

//            dump('passed');
            $passedProvisions[] = $storedProvision;

        }

        if($disableCache === false) {
            $this->passedProvisions = $passedProvisions;
        }

        return $passedProvisions;
    }

    private function evaluateRules(Collection $rules): bool {
        if($rules->isEmpty()) {
            return true;
        }

        $passed = $rules->filter(function (ProvisionRule $rule) {
            $parameter = str_replace('$root.', $this->context->device->root, $rule->parameter);
            $deviceParameter = $this->context->parameterValues->get($parameter);
            if($deviceParameter === null && $this->context->deviceModel !== null) {
                $deviceParameter = DeviceParameter::getParameter($this->context->deviceModel->id, $parameter);
            }

            return $this->condition($deviceParameter?->value, $rule->value, $rule->operator);
        });

        return $passed->count() === $rules->count();
    }

    private function condition(?string $paramValue, string $ruleValue, string $operator) {
        if($operator === 'in') {
            return $this->inCondition($paramValue, $ruleValue);
        } elseif($operator === 'not in') {
            return !$this->inCondition($paramValue, $ruleValue);
        }

        switch ($operator) {
            case "==":  return $paramValue == $ruleValue;
            case "!=": return $paramValue != $ruleValue;
            case ">=": return $paramValue >= $ruleValue;
            case "<=": return $paramValue <= $ruleValue;
            case ">":  return $paramValue >  $ruleValue;
            case "<":  return $paramValue <  $ruleValue;
            default:       return true;
        }
    }

    private function inCondition(?string $paramValue, string $ruleValue) {
        $ruleValue = explode(',', $ruleValue);
        return in_array($paramValue ?? '', $ruleValue);
    }

}
