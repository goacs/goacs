<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Logic\Processors\SetParameterValuesRequestProcessor;
use App\ACS\Types;

class SetParameterValuesProcessorTask extends Task
{

    public function __construct()
    {
        parent::__construct(Types::SetParameterValuesProcessor);
    }

    public function __invoke(Context $context)
    {
        (new SetParameterValuesRequestProcessor($context))();
        $this->done();
    }
}
