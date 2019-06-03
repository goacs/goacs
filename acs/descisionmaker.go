package acs

import (
	"encoding/xml"
	"fmt"
	acsxml "goacs/acs/xml"
	"io"
	"io/ioutil"
	"net/http"
)

func MakeDecision(request *http.Request) {

	buffer, err := ioutil.ReadAll(request.Body)

	if err != io.EOF && err != nil {
		panic(err)
	}

	fmt.Println(string(buffer))

	parseXML(buffer)
}

func parseXML(buffer []byte) (string, acsxml.Envelope) {
	var envelope acsxml.Envelope
	err := xml.Unmarshal(buffer, &envelope)

	var requestType string = acsxml.EMPTY

	if err == nil {
		requestType = envelope.Type()
	}
	//fmt.Printf("Marshalled Data: %s\n", m)

	return requestType, envelope
}
