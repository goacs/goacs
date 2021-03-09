<?php

declare(strict_types=1);


namespace Tests\Unit\Request;


use App\ACS\Entities\Device;
use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Request\InformRequest;
use App\ACS\Response\AddObjectResponse;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetParameterValuesResponse;
use App\ACS\Response\SetParameterValuesResponse;
use App\ACS\XML\XMLParser;
use Tests\TestCase;

class InformRequestTest extends TestCase
{
    public function test_it_return_parameters_collection() {
        $xml = file_get_contents(__DIR__.'/informrequest.xml');
        $parser = new XMLParser((string)$xml);
        $response = new InformRequest($parser->body);
        $this->assertInstanceOf(Device::class, $response->device);
        $this->assertEquals('F4D9C6', $response->device->oui);
        $this->assertEquals('ZNTSA01C5008', $response->device->serialNumber);
        $this->assertEquals('UM806', $response->device->productClass);
        $this->assertEquals('ECONET', $response->device->manufacturer);
    }
}
