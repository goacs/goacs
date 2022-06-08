<?php


namespace App\ACS\Logic\Script;


use App\ACS\Context;

class Sandbox
{
    private array $variables = [];
    private string $script = "";
    /**
     * @var Context
     */
    private Context $context;

    public function __construct(Context $context, string $script) {
        $this->script = $script;
        $this->context = $context;
        $this->addVariable('isNewDevice', $this->context->new);
        $this->addVariable('deviceModel', $this->context->deviceModel);
        $this->addVariable('device', $this->context->device);
        $this->addVariable('root', $this->context->device->root);
        $this->addVariable('func', new Functions($this->context));
    }

    public function addVariable($name, $value) {
        $this->variables[$name] = $value;
    }

    public function run() {
        try {
            extract($this->variables);
            $ret = eval($this->getScript());
        } catch (\Throwable $exception) {
            throw new SandboxException(sprintf("There is error in script on line %d. Message: %s", $exception->getLine(), $exception->getMessage()), $exception->getCode(), $exception);
        }
        return $ret;
    }

    public function getScript(): string {
        $script = '?>'.$this->script;
        return $script;
    }
}
