<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;

use App\ACS\Kick;
use App\ACS\Types;

class KickTask extends Task
{
    public function __construct()
    {
        parent::__construct(Types::Kick);
    }

    public function __invoke(Context $context) {
        Kick::fromDevice($context->deviceModel)->kick();
    }
}
