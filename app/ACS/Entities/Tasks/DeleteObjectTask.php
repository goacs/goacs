<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\Context;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\AddObjectRequest;
use App\ACS\Request\DeleteObjectRequest;
use App\ACS\Request\SetParameterValuesRequest;
use App\ACS\Types;

class DeleteObjectTask extends Task implements WithRequest
{
    public function __construct()
    {
        parent::__construct(Types::DeleteObject);
    }

    public function toRequest(Context $context): ACSRequest
    {
        return new DeleteObjectRequest($context, $this->payload['parameter']);
    }
}
