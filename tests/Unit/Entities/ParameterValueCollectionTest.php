<?php

declare(strict_types=1);


namespace Tests\Unit\Entities;


use App\ACS\Entities\ParameterInfoCollection;
use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use Tests\TestCase;

class ParameterValueCollectionTest extends TestCase
{
    public function test_it_filter_collection_by_dot() {
        $collection = new ParameterValuesCollection();
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree2.";
        $collection->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree2.Test";
        $collection->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.";
        $collection->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.Test";
        $collection->add($parameter);

        $this->assertCount(2, $collection->filterEndsWithDot());
        $this->assertEquals("InternetGatewayDevice.Tree1.Tree2.", $collection->filterEndsWithDot()[0]->name);
    }

    public function test_it_filter_collection_by_chunk_size() {
        $collection = new ParameterValuesCollection();
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.";
        $collection->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree2.Test.Test2";
        $collection->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1";
        $collection->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.Test.Test2.Test3";
        $collection->add($parameter);

        $this->assertCount(4,$collection->filterByChunkCount(1));
        $this->assertCount(3,$collection->filterByChunkCount(2));
        $this->assertCount(2,$collection->filterByChunkCount(4));
    }

    public function test_diff() {
        $dbParams = new ParameterValuesCollection();
        $parameter = new ParameterValueStruct();
        $parameter->name = "A.B";
        $parameter->value = "1";
        $parameter->flag->send = true;
        $dbParams->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "A.B.C";
        $parameter->value = "2";
        $dbParams->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "A.B.C.D";
        $parameter->value = "3";
        $dbParams->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "A.B.C.D.E";
        $parameter->value = "5";
        $dbParams->add($parameter);

        $cpeParams = new ParameterValuesCollection();
        $parameter = new ParameterValueStruct();
        $parameter->name = "A.B";
        $parameter->value = "2";
        $cpeParams->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "A.B.C";
        $parameter->value = "1";
        $cpeParams->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "A.B.C.D";
        $parameter->value = "3";
        $cpeParams->add($parameter);
        $parameter->name = "A.B.C.D.F";
        $parameter->value = "3";
        $cpeParams->add($parameter);

        $diff = $dbParams->diff($cpeParams);
        $diff2 = $cpeParams->diff($dbParams);
        $diffWithSkip = $cpeParams->diff($dbParams, true);


        $this->assertCount(4, $diff);
        $this->assertEquals("1", $diff['A.B']->value);
        $this->assertEquals("2", $diff['A.B.C']->value);
        $this->assertEquals("5", $diff['A.B.C.D.E']->value);

        $this->assertCount(3, $diff2);
        $this->assertEquals("2", $diff2['A.B']->value);
        $this->assertEquals("1", $diff2['A.B.C']->value);
        $this->assertEquals("3", $diff2['A.B.C.D.F']->value);

        $this->assertCount(2, $diffWithSkip);
        $this->assertEquals("1", $diffWithSkip['A.B.C']->value);
        $this->assertEquals("3", $diffWithSkip['A.B.C.D.F']->value);
    }

    public function test_default_sorting() {
        $this->markTestIncomplete();
        $params = [
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.MinAddress',
                'value' => '192.168.1.2',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_Camera-MaxAddress',
                'value' => '192.168.1.199',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_Camera-MinAddress',
                'value' => '192.168.1.180',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_Computer-MaxAddress',
                'value' => '192.168.1.101',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_Computer-MinAddress',
                'value' => '192.168.1.2',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_HGW-MaxAddress',
                'value' => '192.168.1.219',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_HGW-MinAddress',
                'value' => '192.168.1.200',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_Phone-MaxAddress',
                'value' => '192.168.1.169',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_Phone-MinAddress',
                'value' => '192.168.1.150',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_STB-MaxAddress',
                'value' => '192.168.1.139',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.X_STB-MinAddress',
                'value' => '192.168.1.120',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.DHCPConditionalServingPool.1.IPRouters',
                'value' => '192.168.1.1',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.DHCPConditionalServingPool.1.MaxAddress',
                'value' => '192.168.1.254',
                'type' => 'xsd:string',
            ],
            [
                'name' => 'InternetGatewayDevice.LANDevice.1.LANHostConfigManagement.DHCPConditionalServingPool.1.MinAddress',
                'value' => '192.168.1.2',
                'type' => 'xsd:string',
            ],
        ];

        $parameterCollection = new ParameterValuesCollection();
        foreach($params as $param) {
            $parameter = new ParameterValueStruct();
            $parameter->name = $param['name'];
            $parameter->value = $param['value'];
            $parameter->type = $param['type'];
            $parameterCollection->put($parameter->name, $parameter);
        }


    }
}
