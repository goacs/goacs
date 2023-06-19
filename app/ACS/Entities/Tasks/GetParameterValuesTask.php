<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\GetParameterValuesRequest;
use App\ACS\Types;

class GetParameterValuesTask extends Task implements WithRequest
{

    public function __construct()
    {
        parent::__construct(Types::GetParameterValues);
    }

    public function toRequest(Context $context): ACSRequest
    {
        $request = new GetParameterValuesRequest($context);
        $request->setParameters($this->payload['parameters']);
        return $request;
    }
}
