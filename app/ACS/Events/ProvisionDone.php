<?php
declare(strict_types=1);

namespace App\ACS\Events;

use App\Models\Device;

class ProvisionDone
{
    public function __construct(public Device $device)
    {

    }
}
