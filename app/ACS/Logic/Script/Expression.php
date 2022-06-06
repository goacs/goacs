<?php

declare(strict_types=1);


namespace App\ACS\Logic\Script;


use App\ACS\Context;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Expression extends ExpressionLanguage
{
    public function __construct(private Context $context)
    {
        parent::__construct(null, $this->getACSProviders());
    }

    private function getACSProviders(): array {
        return [

        ];
    }
}
