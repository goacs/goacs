<?php

declare(strict_types=1);


namespace Response;


use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Response\AddObjectResponse;
use App\ACS\Response\DeleteObjectResponse;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetParameterValuesResponse;
use App\ACS\Response\SetParameterValuesResponse;
use App\ACS\XML\XMLParser;
use Tests\TestCase;

class DeleteObjectResponseTest extends TestCase
{
    public function test_it_return_parameters_collection() {
        $xml = file_get_contents(__DIR__.'/deleteobjectresponse.xml');
        $parser = new XMLParser((string)$xml);
        $response = new DeleteObjectResponse($parser->body);
        $this->assertEquals(0, $response->getStatus());
    }
}
