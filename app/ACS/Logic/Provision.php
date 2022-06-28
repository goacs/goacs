<?php

namespace App\ACS\Logic;

use App\ACS\Context;
use App\Models\Provision as ProvisionModel;

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

            //Filter Rules


            $passedProvisions[] = $storedProvision;
        }

        return $passedProvisions;
    }
}
