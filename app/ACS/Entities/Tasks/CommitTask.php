<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\ConnectionRequest;
use App\ACS\Types;

class CommitTask extends Task
{
    public function __construct()
    {
        parent::__construct(Types::Commit);
    }

    public function __invoke()
    {
    }
}
