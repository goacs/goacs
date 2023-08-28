<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\RebootRequest;
use App\ACS\Types;

class RebootTask extends Task implements WithRequest
{
    public function __construct()
    {
        parent::__construct(Types::Reboot);
    }
    public function toRequest(Context $context): ACSRequest
    {
        return new RebootRequest($context);
    }
}
