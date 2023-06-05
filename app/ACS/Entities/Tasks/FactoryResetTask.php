<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\FactoryResetRequest;

class FactoryResetTask extends Task implements WithRequest
{

    public function toRequest(Context $context): ACSRequest
    {
        return new FactoryResetRequest($context);
    }
}
