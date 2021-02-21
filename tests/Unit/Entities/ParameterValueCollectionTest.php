<?php

declare(strict_types=1);


namespace Entities;


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
        $collection1 = new ParameterValuesCollection();
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree2.Test.Test2";
        $parameter->value = "1";
        $collection1->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1";
        $parameter->value = "2";
        $collection1->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.Test.Test2.Test3";
        $parameter->value = "3";
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.Test.Test2.Test2";
        $parameter->value = "5";
        $collection1->add($parameter);

        $collection2 = new ParameterValuesCollection();
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree2.Test.Test2";
        $parameter->value = "2";
        $collection2->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1";
        $parameter->value = "1";
        $collection2->add($parameter);
        $parameter = new ParameterValueStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.Test.Test2.Test3";
        $parameter->value = "3";
        $collection2->add($parameter);

        $diff = $collection1->diff($collection2);

        $this->assertCount(3, $diff);
        $this->assertEquals("1", $diff['InternetGatewayDevice.Tree1.Tree2.Test.Test2']->value);
        $this->assertEquals("2", $diff['InternetGatewayDevice.Tree1']->value);
        $this->assertEquals("5", $diff['InternetGatewayDevice.Tree1.Tree3.Test.Test2.Test2']->value);
    }
}
