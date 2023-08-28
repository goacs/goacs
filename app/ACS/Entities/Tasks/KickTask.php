<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;

use App\ACS\Kick;
use App\ACS\Types;
use App\Models\Log;

class KickTask extends Task
{
    public function __construct()
    {
        parent::__construct(Types::Kick);
    }

    public function __invoke(Context $context): bool {
        Log::logInfoFromDevice($context->deviceModel, 'KICK REQUEST');
        if(Kick::fromDevice($context->deviceModel)->kick()) {
            Log::logInfoFromDevice($context->deviceModel, 'KICK SUCCESSFUL');
            return true;
        } else {
            Log::logInfoFromDevice($context->deviceModel, 'KICK FAILED');
            return false;
        }
    }
}
