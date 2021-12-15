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

    public string $cwmpUri = '';

    public string $requestId = '';

    private array $reverseNamespaces = [];


    public function __construct(string $reqBodyContent) {
        $this->xml = $reqBodyContent;
        $this->parse();
    }

    public function parse() {
        if(empty($this->xml)) {
            $this->bodyType = Types::EMPTY;
            return;
        }

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->loadXml($this->xml);
        $this->xml = $dom->saveXML();
        unset($dom);
        $xml = new Crawler($this->xml);

        foreach($this->lookupNamespaces() as $namespace)
        {
            $xml->registerNamespace($namespace[1], $namespace[2]);
            $this->reverseNamespaces[$namespace[2]] = $namespace[1];
        }

        try {
            $soapEnvNs = $this->reverseNamespaces['http://schemas.xmlsoap.org/soap/envelope/'];
            $this->header = $xml->evaluate("/${soapEnvNs}:Envelope/${soapEnvNs}:Header")->getNode(0);
            $this->body = $xml->evaluate("/${soapEnvNs}:Envelope/${soapEnvNs}:Body")->children()->getNode(0);
        } catch (\Exception $exception) {
            throw new ACSException("INVALID XML");
        }

        $this->bodyType = $this->body->localName;
        $this->cwmpUri = (string)$this->body->lookupNamespaceURI('cwmp');
        $this->fillCwmpVersion($this->cwmpUri);
        $this->extractRequestId();
    }

    private function lookupNamespaces(): array {
        $matches = [];
        if(preg_match_all('/xmlns:([^=]*)="([^"]*)"/', $this->xml, $matches, PREG_SET_ORDER)) {
            return $matches;
        }

        return [];
    }

    /**
     * @return array
     */
    public function getReverseNamespaces(): array
    {
        return $this->reverseNamespaces;
    }



    private function fillCwmpVersion(string $uri) {
        switch ($uri) {
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
            case 'urn:dslforum-org:cwmp-1-0':
                $this->cwmpVersion = '1.0';
                break;
        }
    }

    private function extractRequestId() {
        if($this->header !== null) {
            foreach ($this->header->childNodes as $child) {
                if ($child instanceof \DOMElement && $child->localName === 'ID') {
                    $this->requestId = $child->firstChild->nodeValue;
                    return;
                }
            }
        } else {
            //todo: quick workaround for request which not contains header
            $this->requestId = sha1(microtime());
        }
    }

    public static function normalize(string $xml): string {
        $dom = new \DOMDocument();
        $dom->loadXML( $xml, LIBXML_NOBLANKS | LIBXML_COMPACT );
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput  = false;
        return $dom->saveXML();
    }
}
