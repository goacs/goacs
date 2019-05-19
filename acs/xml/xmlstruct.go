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
