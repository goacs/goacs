<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\SetParameterValuesRequest;
use App\ACS\Types;

class SetParameterValuesTask extends Task
{

    public function __construct()
    {
        parent::__construct(Types::SetParameterValues);
        $this->items = new ParameterValuesCollection();
    }

    public function toRequest(Context $context): ACSRequest
    {
        return new SetParameterValuesRequest($context, $this->items);
    }
}
