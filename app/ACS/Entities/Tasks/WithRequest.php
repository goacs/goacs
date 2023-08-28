<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Request\ACSRequest;

interface WithRequest
{
    public function toRequest(Context $context): ACSRequest;
}
