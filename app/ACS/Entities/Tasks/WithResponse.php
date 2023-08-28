<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Response\ACSResponse;

interface WithResponse
{
    public function toResponse(Context $context): ACSResponse;
}
