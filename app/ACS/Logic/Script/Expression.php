<?php

declare(strict_types=1);


namespace App\ACS\Logic\Script;


use App\ACS\Context;
use App\ACS\Logic\Script\Providers\DeviceFunctionsProvider;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Expression extends ExpressionLanguage
{
    public function __construct(private Context $context)
    {
        parent::__construct(null, $this->getACSProviders());
    }

    public function run(\Symfony\Component\ExpressionLanguage\Expression|string $expression): mixed
    {
        return parent::evaluate($expression, $this->getACSVariables());
    }

    private function getACSProviders(): array {
        return [
            new DeviceFunctionsProvider($this->context)
        ];
    }

    private function getACSVariables(): array {

        return [
            'isNewDevice' => $this->context->new,
            'deviceModel' => $this->context->deviceModel,
            'device' => $this->context->device,
            'root' => $this->context->device->root,
        ];
    }
}
