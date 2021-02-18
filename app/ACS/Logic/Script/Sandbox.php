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
        $this->addVariable('deviceModel', $this->context->deviceModel);
        $this->addVariable('device', $this->context->device);
        $this->addVariable('func', new Functions($this->context));
    }

    public function addVariable($name, $value) {
        $this->variables[$name] = $value;
    }

    public function run() {
        extract($this->variables);
        $ret = eval($this->getScript());
        return $ret;
    }

    public function getScript(): string {
        $script = $this->script;
        $script = str_replace('<?php', '', $script);
        $script = str_replace("\n",'', $script);
        return $script;
    }
}
