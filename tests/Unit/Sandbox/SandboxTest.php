<?php


namespace Sandbox;


use App\ACS\Context;
use App\ACS\Logic\Script\Sandbox;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class SandboxTest extends TestCase
{
    public function test_sandbox_eval() {

        $script = '
$test = "123";
$ret = $test;
$ret .= $obj->dupa;
$obj->dupa = "555";
$ret .= $obj->dupa;
return $ret;';

        $obj = new \stdClass();
        $obj->dupa = "33333";
        $sandbox = $this->newSandbox($script);
        $sandbox->addVariable('obj', $obj);
        $ret = $sandbox->run();
        $this->assertEquals('12333333555', $ret);
        $this->assertEquals('555', $obj->dupa);
    }

    public function test_device_save() {

    }

    private function newSandbox(string $script) {
        return new Sandbox(new Context(new Request(), new Response()), $script);
    }
}
