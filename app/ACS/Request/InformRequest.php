<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Entities\Device;
use App\ACS\Entities\Event;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\XML\ParameterListReader;
use Illuminate\Support\Collection;

class InformRequest extends CPERequest
{
    public Device $device;
    public ParameterValuesCollection $parametersList;
    public Collection $events;

    public function __construct(\DOMNode $body) {
        parent::__construct($body);
        $this->events = new Collection();
        $this->parametersList = new ParameterValuesCollection();
        $this->readValues();
    }

    public function readValues() {
        foreach($this->body->childNodes as $child) {
            if($child instanceof \DOMElement) {
                if($child->nodeName === "DeviceId"){
                    $this->readDeviceId($child);
                } else if ($child->nodeName === "Event") {
                    $this->readEvent($child);
                } else if ($child->nodeName === "ParameterList") {
                    $parameterListReader = new ParameterListReader($child);
                    $this->parametersList = $parameterListReader->parameters();
                }
            }
        }
    }

    private function readDeviceId(\DOMElement $child)
    {
        $this->device = new Device();
        $xpath = new \DOMXPath($child->ownerDocument);
        /** @var \DOMElement $param */
        foreach ($xpath->query("//DeviceId/*") as $param)
        {
            switch ($param->nodeName) {
                case 'Manufacturer':
                    $this->device->manufacturer = $param->firstChild->nodeValue;
                    break;
                case 'OUI':
                    $this->device->oui = $param->firstChild->nodeValue;
                    break;
                case 'ProductClass':
                    $this->device->productClass = $param->firstChild->nodeValue;
                    break;
                case 'ModelName':
                    $this->device->modelName = $param->firstChild->nodeValue;
                    break;
                case 'SerialNumber':
                    $this->device->serialNumber = $param->firstChild->nodeValue;
                    break;
            }
        }

    }

    private function readEvent(\DOMElement $child)
    {
        $xpath = new \DOMXPath($child->ownerDocument);
        /** @var \DOMElement $param */
        foreach ($xpath->query("//Event/EventStruct") as $param) {
            $event = new Event();
            $event->setCode($param->getElementsByTagName('EventCode')->item(0)->nodeValue);
            $event->key = $param->getElementsByTagName('CommandKey')->item(0)->nodeValue;
            $this->events[] = $event;
        }
    }

    public function hasEvent(int $number): bool {
        return $this->events->filter(fn(Event $item) => $item->getCodeNumber() === $number)->count() > 0;
    }
}
