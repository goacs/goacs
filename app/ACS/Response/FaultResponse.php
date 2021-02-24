<?php

declare(strict_types=1);


namespace App\ACS\Response;


use App\ACS\XML\XMLParser;

class FaultResponse extends CPEResponse
{
    /**
     * @var XMLParser
     */
    private XMLParser $parser;

    public \stdClass $detail;

    public string $faultCode = '';
    public string $faultString = '';

    public function __construct(XMLParser $parser) {
        parent::__construct($parser->body);
        $this->parser = $parser;
        $this->detail = new \stdClass();
        $this->readValues();
    }

    private function readValues()
    {
        $xpath = new \DOMXPath($this->body->ownerDocument);
        $soapEnvNs = $this->parser->getReverseNamespaces()['http://schemas.xmlsoap.org/soap/envelope/'];
        /** @var \DOMElement $statusElement */
        $this->faultCode = $xpath->query("//$soapEnvNs:Fault/faultcode")->item(0)->nodeValue;
        $this->faultString = $xpath->query("//$soapEnvNs:Fault/faultstring")->item(0)->nodeValue;
        $detail = $xpath->query("//$soapEnvNs:Fault/detail")->item(0);
        $this->readDetails($xpath);
    }

    private function readDetails(?\DOMXPath $xpath)
    {
        $cwmpNS = $this->parser->getReverseNamespaces()[$this->parser->cwmpUri];

//        $this->detail->FaultCode = $xpath->query("//$cwmpNS:Fault");
        $this->detail->faultCode = $xpath->query("//$cwmpNS:Fault/FaultCode")->item(0)->nodeValue;
        $this->detail->faultString = $xpath->query("//$cwmpNS:Fault/FaultCode")->item(0)->nodeValue;

        /** @var \DOMNodeList $spvFaults */
        $spvFaults = $xpath->query("//$cwmpNS:Fault/SetParameterValuesFault");
        if(count($spvFaults) > 0) {
            $this->detail->spvfault = [];
            /** @var \DOMElement $spvFault */
            foreach ($spvFaults as $spvFault) {
                $pm = $spvFault->getElementsByTagName('ParameterName')->item(0)->nodeValue;
                $faultCode = $spvFault->getElementsByTagName('FaultCode')->item(0)->nodeValue;
                $faultString = $spvFault->getElementsByTagName('FaultString')->item(0)->nodeValue;
                $this->detail->spvfault[] = [
                    'ParameterName' => $pm,
                    'faultCode' => $faultCode,
                    'faultString' => $faultString,
                ];
            }
        }
    }
}
