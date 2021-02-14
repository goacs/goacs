<?php

declare(strict_types=1);


namespace Response;


use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\XML\XMLParser;
use Tests\TestCase;

class GetParameterNamesResponseTest extends TestCase
{
    public function test_it_return_parameters_collection() {
        $xml = file_get_contents(__DIR__.'/gpnresponse.xml');
        $parser = new XMLParser((string)$xml);
        $response = new GetParameterNamesResponse($parser->body);
        $this->assertNotEmpty($response->parameters->first()->name);
        $this->assertNotEmpty($response->parameters->last()->name);
        $this->assertCount(9263,$response->parameters);
        $this->assertInstanceOf(ParameterInfoStruct::class, $response->parameters->first());
    }
}
