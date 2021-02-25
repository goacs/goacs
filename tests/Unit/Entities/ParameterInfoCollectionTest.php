<?php

declare(strict_types=1);


namespace Tests\Unit\Entities;


use App\ACS\Entities\ParameterInfoCollection;
use App\ACS\Entities\ParameterInfoStruct;
use Tests\TestCase;

class ParameterInfoCollectionTest extends TestCase
{
    public function test_it_filter_collection_by_dot() {
        $collection = new ParameterInfoCollection();
        $parameter = new ParameterInfoStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree2.";
        $collection->add($parameter);
        $parameter = new ParameterInfoStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree2.Test";
        $collection->add($parameter);
        $parameter = new ParameterInfoStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.";
        $collection->add($parameter);
        $parameter = new ParameterInfoStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.Test";
        $collection->add($parameter);

        $this->assertCount(2, $collection->filterEndsWithDot());
        $this->assertEquals("InternetGatewayDevice.Tree1.Tree2.", $collection->filterEndsWithDot()[0]->name);
    }

    public function test_it_filter_collection_by_chunk_size() {
        $collection = new ParameterInfoCollection();
        $parameter = new ParameterInfoStruct();
        $parameter->name = "InternetGatewayDevice.";
        $collection->add($parameter);
        $parameter = new ParameterInfoStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree2.Test.Test2";
        $collection->add($parameter);
        $parameter = new ParameterInfoStruct();
        $parameter->name = "InternetGatewayDevice.Tree1";
        $collection->add($parameter);
        $parameter = new ParameterInfoStruct();
        $parameter->name = "InternetGatewayDevice.Tree1.Tree3.Test.Test2.Test3";
        $collection->add($parameter);

        $this->assertCount(4,$collection->filterByChunkCount(1));
        $this->assertCount(3,$collection->filterByChunkCount(2));
        $this->assertCount(2,$collection->filterByChunkCount(4));
    }
}
