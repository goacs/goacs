<?php

declare(strict_types=1);


namespace Response;


use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Response\AddObjectResponse;
use App\ACS\Response\FaultResponse;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetParameterValuesResponse;
use App\ACS\Response\SetParameterValuesResponse;
use App\ACS\XML\XMLParser;
use Tests\TestCase;

class FaultTest extends TestCase
{
    public function test_parse_without_spv() {
        $xml = file_get_contents(__DIR__.'/fault.xml');
        $parser = new XMLParser((string)$xml);
        $response = new FaultResponse($parser);

        $this->assertEquals("Client", $response->faultCode);
        $this->assertEquals("CWMP fault", $response->faultString);
    }

    public function test_parse_with_spv() {
        $xml = file_get_contents(__DIR__.'/faultwithspv.xml');
        $parser = new XMLParser((string)$xml);
        $response = new FaultResponse($parser);

        $this->assertEquals("Client", $response->faultCode);
        $this->assertEquals("CWMP fault", $response->faultString);
        $this->assertCount(2, $response->detail->spvfault);
    }
}
