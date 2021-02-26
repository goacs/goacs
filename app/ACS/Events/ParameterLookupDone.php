<?php


namespace App\ACS\Events;


use App\ACS\Entities\ParameterValuesCollection;
use App\Models\Device;

class ParameterLookupDone
{

    public function __construct(public Device $device, public ParameterValuesCollection $parameters) {}
}
