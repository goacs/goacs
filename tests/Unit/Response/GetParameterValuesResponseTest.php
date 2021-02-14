<?php

declare(strict_types=1);


namespace Response;


use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetParameterValuesResponse;
use App\ACS\XML\XMLParser;
use Tests\TestCase;

class GetParameterValuesResponseTest extends TestCase
{
    public function test_it_return_parameters_collection() {
        $xml = file_get_contents(__DIR__.'/gpvresponse.xml');
        $parser = new XMLParser((string)$xml);
        $response = new GetParameterValuesResponse($parser->body);
        $this->assertNotEmpty($response->parameters->first()->value);
        $this->assertNotEmpty($response->parameters->first()->name);
        $this->assertNotEmpty($response->parameters->last()->value);
        $this->assertNotEmpty($response->parameters->last()->name);
        $this->assertCount(7, $response->parameters);
        $this->assertInstanceOf(ParameterValueStruct::class, $response->parameters->first());
    }
}
