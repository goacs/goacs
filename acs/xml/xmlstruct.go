package xml

import "encoding/xml"

type Envelope struct {
	XMLName xml.Name `xml:"Envelope"`
	Header Header `xml:"Header"`
	Body Body `xml:"Body"`
}

type Header struct {
	XMLName xml.Name `xml:"Header"`
	ID struct{
		MustUnderstand int8 `xml:"mustUnderstand,attr"`
		Value string `xml:",chardata"`
	}
}

type Body struct {
	XMLName xml.Name `xml:"Body"`
	Inform

}

type Inform struct {
	XMLName xml.Name `xml:"Inform"`
	DeviceId DeviceId `xml:"DeviceId"`
	ParameterList []ParameterValue `xml:"ParameterList"`
}

type ParameterValue struct {
	Name string `xml:"Name"`
	Value string `xml:"Value"`
}

type DeviceId struct {
	/* 
	      <DeviceId>
	        <Manufacturer>Huawei Technologies Co., Ltd.</Manufacturer>
	        <OUI>202BC1</OUI>
	        <ProductClass>BM632w</ProductClass>
	        <SerialNumber>000000</SerialNumber>
	      </DeviceId>
	 */
	Manufacturer string `xml:"Manufacturer"`
	OUI string `xml:"OUI"`
	ProductClass string `xml:"ProductClass"`
	SerialNumber string `xml:"SerialNumber"`
}