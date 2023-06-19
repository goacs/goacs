<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Response\ACSResponse;
use App\ACS\Response\InformResponse;
use App\ACS\Types;

class InformResponseTask extends Task implements WithResponse
{
    public function __construct() {
        parent::__construct(Types::INFORMResponse);
    }
    public function toResponse(Context $context): ACSResponse
    {
        return new InformResponse($context);
    }
}
