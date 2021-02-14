<?php

declare(strict_types=1);


namespace Response;


use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetParameterValuesResponse;
use App\ACS\Response\SetParameterValuesResponse;
use App\ACS\XML\XMLParser;
use Tests\TestCase;

class SetParameterValuesResponseTest extends TestCase
{
    public function test_it_return_parameters_collection() {
        $xml = file_get_contents(__DIR__.'/spvresponse.xml');
        $parser = new XMLParser((string)$xml);
        $response = new SetParameterValuesResponse($parser->body);
        $this->assertEquals(0, $response->getStatus());
    }
}
