<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Entities\Device;
use App\ACS\Entities\Event;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\XML\ParameterListReader;
use Illuminate\Support\Collection;

class GetRPCMethodsCPERequest extends CPERequest
{
    public Device $device;
    public ParameterValuesCollection $parametersList;
    public Collection $events;

    public function __construct(\DOMNode $body) {
        parent::__construct($body);

    }
}
