<?php


namespace Tests\Unit\Sandbox;


use App\ACS\Context;
use App\ACS\Logic\Script\Sandbox;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class SandboxTest extends TestCase
{
    public function test_sandbox_eval() {

        $script = '
<?php
$test = "123";
$ret = $test;
$ret .= $obj->value;
$obj->value = "555";
$ret .= $obj->value;
return $ret;';

        $obj = new \stdClass();
        $obj->value = "33333";
        $sandbox = $this->newSandbox($script);
        $sandbox->addVariable('obj', $obj);
        $ret = $sandbox->execute();
        $this->assertEquals('12333333555', $ret);
        $this->assertEquals('555', $obj->value);
    }


    private function newSandbox(string $script) {
        $context = new Context(new Request(), new Response());
        $context->deviceModel = Device::factory()->create();
        return new Sandbox($context, $script);
    }
}
