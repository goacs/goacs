<?php


namespace Tests\Integration;


use App\ACS\Entities\Flag;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Logic\DeviceParametersLogic;
use App\Models\Device;
use Tests\TestCase;

class DeviceParametersLogicTest extends TestCase
{
    public function test_device_parameters_with_templates() {
        $device = Device::factory()->create();

        $deviceParameters = new DeviceParametersLogic($device);
        $parameters = $deviceParameters->combinedDeviceParametersWithTemplates();
        $this->markTestIncomplete('Incomplete device templates parameters test');
    }

    public function test_getParametersToCreateInstance() {
        $device = Device::factory()->create();
        $deviceParameters = new DeviceParametersLogic($device);
        $flag = new Flag(true, true, true, true);

        $sessionParams = new ParameterValuesCollection([
            'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.' => (new ParameterValueStruct())->setName('InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.')->setFlag($flag),
        ]);

        $dbParams = new ParameterValuesCollection([
            'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.2.' => (new ParameterValueStruct())->setName('InternetGatewayDevice.WANDevice.1.WANConnectionDevice.2.')->setFlag($flag),
            'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.3.' => (new ParameterValueStruct())->setName('InternetGatewayDevice.WANDevice.1.WANConnectionDevice.3.')->setFlag($flag),
            'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.2.WANPPPConnection.1.' => (new ParameterValueStruct())->setName('InternetGatewayDevice.WANDevice.1.WANConnectionDevice.2.WANPPPConnection.1.')->setFlag($flag),
            'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.3.WANPPPConnection.1.' => (new ParameterValueStruct())->setName('InternetGatewayDevice.WANDevice.1.WANConnectionDevice.3.WANPPPConnection.1.')->setFlag($flag),
        ]);

        $toAdd = $deviceParameters->getParametersToCreateInstance($sessionParams, $dbParams);

        $this->assertCount(4, $toAdd);
        $this->assertEquals($toAdd[0]->name, "InternetGatewayDevice.WANDevice.1.WANConnectionDevice.");
        $this->assertEquals($toAdd[1]->name, "InternetGatewayDevice.WANDevice.1.WANConnectionDevice.");
        $this->assertEquals($toAdd[2]->name, "InternetGatewayDevice.WANDevice.1.WANConnectionDevice.2.WANPPPConnection.");
        $this->assertEquals($toAdd[3]->name, "InternetGatewayDevice.WANDevice.1.WANConnectionDevice.3.WANPPPConnection.");
    }
}
