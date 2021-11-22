<?php

declare(strict_types=1);


namespace App\ACS\Response;


use Carbon\Carbon;

class DownloadResponse extends CPEResponse
{
    public int $status = 0;
    public Carbon $startTime;
    public Carbon $completeTime;

    public function __construct(\DOMNode $body) {
        parent::__construct($body);
        $this->readValues();
    }

    private function readValues()
    {
        $xpath = new \DOMXPath($this->body->ownerDocument);
        /** @var \DOMElement $statusElement */
        $statusElement = $xpath->query("//cwmp:DownloadResponse/Status")->item(0);
        $this->startTime = Carbon::parse($xpath->query("//cwmp:DownloadResponse/StartTime")->item(0)?->nodeValue);
        $this->completeTime = Carbon::parse($xpath->query("//cwmp:DownloadResponse/CompleteTime")->item(0)?->nodeValue);
        $this->status = (int) $statusElement->nodeValue;
    }
}
