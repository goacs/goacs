package xml

import (
	"encoding/xml"
	"strings"
)

type Envelope struct {
	XMLName xml.Name `xml:"Envelope"`
	Header  Header   `xml:"Header"`
	Body    Body     `xml:"Body"`
}

type Header struct {
	XMLName xml.Name `xml:"Header"`
	ID      string   `xml:"ID"`
}

type Body struct {
	Message XMLMessage `xml:",any"`
}

type XMLMessage struct {
	XMLName xml.Name
}

type Inform struct {
	DeviceId      DeviceId         `xml:"Body>Inform>DeviceId"`
	Events        []Event          `xml:"Body>Inform>Event>EventStruct"`
	ParameterList []ParameterValue `xml:"ParameterList"`
}

type Event struct {
	EventCode  string
	CommandKey string
}

type ParameterValue struct {
	Name  string `xml:"Name"`
	Value string `xml:"Value"`
}

type DeviceId struct {
	Manufacturer string
	OUI          string
	ProductClass string
	SerialNumber string
}

func (envelope *Envelope) Type() string {
	return strings.ToLower(envelope.Body.Message.XMLName.Local)
}

func (envelope *Envelope) InformResponse() string {
	return `<soapenv:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cwmp="urn:dslforum-org:cwmp-1-0" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soapenv:Header>
        <cwmp:ID soapenv:mustUnderstand="1">` + envelope.Header.ID + `</cwmp:ID>
    </soapenv:Header>
    <soapenv:Body>
        <cwmp:InformResponse>
            <MaxEnvelopes>1</MaxEnvelopes>
        </cwmp:InformResponse>
    </soapenv:Body>
</soapenv:Envelope>`
}

func (inform *Inform) isBootEvent() bool {
	for idx := range inform.Events {
		if inform.Events[idx].EventCode == "0 BOOTSTRAP" ||
			inform.Events[idx].EventCode == "1 BOOT" {
			return true
		}
	}

	return false
}
