<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Logic\Processors\SetParameterValuesRequestProcessor;

class SetParameterValuesProcessorTask extends Task
{
    public function __invoke(Context $context)
    {
        (new SetParameterValuesRequestProcessor($context))();
        $this->done();
    }
}
