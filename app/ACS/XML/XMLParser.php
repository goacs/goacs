<?php

declare(strict_types=1);


namespace App\ACS\XML;


use App\ACS\ACSException;
use App\ACS\Types;
use Laravie\Parser\Xml\Document;
use Laravie\Parser\Xml\Reader;
use Symfony\Component\DomCrawler\Crawler;

class XMLParser
{
    private string $xml;

    public \DOMNode|null $body;

    public \DOMNode|null $header;

    public string $bodyType;

    public string $cwmpVersion = '1.0';


    public function __construct(string $reqBodyContent) {
        $this->xml = $reqBodyContent;
        $this->parse();
    }

    public function parse() {
//        dump("XML", $this->xml);
        if(empty($this->xml)) {
            $this->bodyType = Types::EMPTY;
            return;
        }
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXml($this->xml);
        $this->xml = $dom->saveXML();
        unset($dom);
        $xml = new Crawler($this->xml);

        foreach($this->lookupNamespaces() as $namespace)
        {
            $xml->registerNamespace($namespace[1], $namespace[2]);
        }

        try {
            $this->header = $xml->evaluate('/SOAP-ENV:Envelope/SOAP-ENV:Header')->getNode(0);
            $this->body = $xml->evaluate('/SOAP-ENV:Envelope/SOAP-ENV:Body')->children()->getNode(0);
        } catch (\Exception $exception) {
            dump($exception->getMessage());
            throw new ACSException("INVALID XML");
        }

        $this->bodyType = $this->body->localName;
        $this->fillCwmpVersion($this->body->namespaceURI);
    }

    private function lookupNamespaces(): array {
        $matches = [];
        if(preg_match_all('/xmlns:([^=]*)="([^"]*)"/', $this->xml, $matches, PREG_SET_ORDER)) {
            return $matches;
        }

        return [];
    }

    private function fillCwmpVersion(string $uri) {
        switch ($uri) {
            case 'urn:dslforum-org:cwmp-1-0':
                $this->cwmpVersion = '1.0';
                break;
            case 'urn:dslforum-org:cwmp-1-1':
                $this->cwmpVersion = '1.1';
                break;
            case 'urn:dslforum-org:cwmp-1-2':
                $this->cwmpVersion = '1.2';
                break;
            case 'urn:dslforum-org:cwmp-1-3':
                $this->cwmpVersion = '1.3';
                break;
            default:
                throw new ACSException("Unknown cwmp version");
        }
    }
}
