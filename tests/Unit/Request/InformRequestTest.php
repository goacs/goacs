<?php

declare(strict_types=1);


namespace Tests\Unit\Request;


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
        dump($response->events);
    }
}
