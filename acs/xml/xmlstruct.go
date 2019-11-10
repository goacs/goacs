package xml

import (
	"encoding/xml"
	"strconv"
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

type ParameterInfo struct {
	Name     string `xml:"Name"`
	Writable string `xml:"Writable"`
}

type DeviceId struct {
	Manufacturer string
	OUI          string
	ProductClass string
	SerialNumber string
}

type GetParameterNamesResponse struct {
	ParameterList []ParameterInfo `xml:"Body>GetParameterNamesResponse>ParameterList>ParameterInfoStruct"`
}

type GetParameterValuesResponse struct {
	ParameterList []ParameterValue `xml:"Body>GetParameterValuesResponse>ParameterList>ParameterValueStruct"`
}

type ACSBool bool

func (abool *ACSBool) UnmarshalXMLAttr(attr xml.Attr) (err error) {

	if attr.Value == "0" {
		*abool = false
	}

	*abool = true

	return nil
}

func (abool ACSBool) String() string {
	if abool == true {
		return "1"
	}
	return "0"
}

func (envelope *Envelope) Type() string {
	return strings.ToLower(envelope.Body.Message.XMLName.Local)
}

func (envelope *Envelope) InformResponse() string {
	return `<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cwmp="urn:dslforum-org:cwmp-1-0" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
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

func (inform *Inform) IsEvent(event string) bool {
	for idx := range inform.Events {
		if inform.Events[idx].EventCode == event {
			return true
		}
	}

	return false
}

func (inform *Inform) IsBootEvent() bool {
	for idx := range inform.Events {
		if inform.Events[idx].EventCode == "0 BOOTSTRAP" ||
			inform.Events[idx].EventCode == "1 BOOT" {
			return true
		}
	}

	return false
}

func (envelope *Envelope) GPNRequest(path string) string {
	return `<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cwmp="urn:dslforum-org:cwmp-1-0" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soapenv:Header>
        <cwmp:ID soapenv:mustUnderstand="1">` + envelope.Header.ID + `</cwmp:ID>
    </soapenv:Header>
    <soapenv:Body>
        <cwmp:GetParameterNames>
			<ParameterPath>` + path + `</ParameterPath>
			<NextLevel>false</NextLevel>
		</cwmp:GetParameterNames>
    </soapenv:Body>
</soapenv:Envelope>`
}

//TODO: zrobić ładniej ;)
func (envelope *Envelope) GPVRequest(info []ParameterInfo) string {
	request := `<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cwmp="urn:dslforum-org:cwmp-1-0" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <soapenv:Header>
      <cwmp:ID soapenv:mustUnderstand="1">` + envelope.Header.ID + `</cwmp:ID>
  </soapenv:Header>
  <soapenv:Body>
      <cwmp:GetParameterValues>
			<ParameterNames soapenc:arrayType=xsd:string[` + strconv.Itoa(len(info)) + `]>
`
	for _, parameter := range info {
		request += `<string>` + parameter.Name + `</string>`
		request += "\n"
	}

	request += `</ParameterNames>
		</cwmp:GetParameterValues>
  </soapenv:Body>
</soapenv:Envelope>`

	return request
}

func (envelope *Envelope) GetRPCMethodsRequest() string {
	return `<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cwmp="urn:dslforum-org:cwmp-1-0" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <soapenv:Header>
        <cwmp:ID soapenv:mustUnderstand="1">` + envelope.Header.ID + `</cwmp:ID>
    </soapenv:Header>
    <soapenv:Body>
        <cwmp:GetRPCMethods>
		</cwmp:GetRPCMethods>
    </soapenv:Body>
</soapenv:Envelope>`
}
