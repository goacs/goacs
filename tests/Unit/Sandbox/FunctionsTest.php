<?php


namespace Sandbox;


use App\ACS\Context;
use App\ACS\Entities\Device;
use App\ACS\Logic\Script\Functions;
use App\ACS\Logic\Script\Sandbox;
use App\Models\DeviceParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class FunctionsTest extends TestCase
{
    public function test_set_parameter() {
        $context = $this->newContext();
        $sandbox = $this->newSandbox($context, file_get_contents(__DIR__.'/scripts/setparameter.php'));
        $sandbox->run();
        $this->assertDatabaseHas('device_parameters',[
            'device_id' => $context->deviceModel->id,
            'name' => 'A.B.C.D',
            'value' => 'test'
        ]);
        $this->assertDatabaseHas('device_parameters',[
            'device_id' => $context->deviceModel->id,
            'name' => 'A.B.C.D.E',
            'value' => 'test2'
        ]);
        $this->assertDatabaseHas('device_parameters',[
           'device_id' => $context->deviceModel->id,
           'name' => 'A.B.C.D.D',
           'value' => 'test3'
        ]);

        $context->deviceModel->parameters()->forceDelete();
        $context->deviceModel->forceDelete();
    }

    public function test_get_parameter() {
        $context = $this->newContext();
        $sandbox = $this->newSandbox($context, file_get_contents(__DIR__.'/scripts/getparameter.php'));
        $ret = $sandbox->run();
        $this->assertEquals('test', $ret);

        $context->deviceModel->parameters()->forceDelete();
        $context->deviceModel->forceDelete();
    }

    public function test_parameter_exist() {
        $context = $this->newContext();
        $sandbox = $this->newSandbox($context, file_get_contents(__DIR__.'/scripts/parameterexist.php'));
        $ret = $sandbox->run();
        $this->assertFalse($ret);

        DeviceParameter::setParameter($context->deviceModel->id, 'A.B.C.D', 'test', 'RWS', 'xsd:string');
        $ret = $sandbox->run();
        $this->assertTrue($ret);

        $context->deviceModel->parameters()->forceDelete();
        $context->deviceModel->forceDelete();
    }

    private function newSandbox(Context $context, string $script) {
        return new Sandbox($context, $script);
    }

    public function newContext(): Context {
        $context = new Context(new Request(), new Response());
        $context->device = new Device();
        $context->deviceModel = \App\Models\Device::factory()->create();

        return $context;
    }
}
