<?php

namespace App\ACS\Logic;

use App\ACS\Context;
use App\Models\Provision as ProvisionModel;
use App\Models\ProvisionRule;
use Illuminate\Support\Collection;

class Provision
{
    public function __construct(private Context $context) {}

    public function getProvisions(): array {
        /** @var ProvisionModel[] $storedProvisions */
        $storedProvisions = ProvisionModel::with(['rules','denied'])->get();

        $passedProvisions = [];
        foreach ($storedProvisions as $storedProvision) {
            //Filter Events
            if ($storedProvision->event !== '' && count(array_intersect($storedProvision->eventsArray(), $this->context->events)) === 0) {
                continue;
            }

            //Filter Request
            if(in_array($this->context->bodyType, $storedProvision->requestsArray()) === false) {
                continue;
            }

            //Filter Rules
            if($this->evaluateRules($storedProvision->rules) === false) {
                continue;
            }

            $passedProvisions[] = $storedProvision;
        }

        return $passedProvisions;
    }

    private function evaluateRules(Collection $rules): bool {
        if($rules->isEmpty()) {
            return true;
        }

        return $rules->filter(function (ProvisionRule $rule) {
            $parameterValue = $this->context->parameterValues->get($rule->parameter);
            if($parameterValue !== null) {
                return $this->condition($parameterValue, $rule->value, $rule->operator);
            }

            return false;
        })->isNotEmpty();
    }

    private function condition(mixed $var1, mixed $var2, string $operator) {
        switch ($operator) {
            case "=":  return $var1 == $var2;
            case "!=": return $var1 != $var2;
            case ">=": return $var1 >= $var2;
            case "<=": return $var1 <= $var2;
            case ">":  return $var1 >  $var2;
            case "<":  return $var1 <  $var2;
            default:       return true;
        }
    }

}
