<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\GetParameterNamesRequest;
use App\ACS\Types;

class GetParameterNamesTask extends Task implements WithRequest
{
    public function __construct()
    {
        parent::__construct(Types::GetParameterNames);
    }

    public function toRequest(Context $context): ACSRequest
    {
        return new GetParameterNamesRequest($context, $this->payload['parameters']);
    }
}
