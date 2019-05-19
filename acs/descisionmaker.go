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

	parseXML(buffer)
}

func parseXML(buffer []byte) {
	var envelope acsxml.Envelope
	err := xml.Unmarshal(buffer, &envelope)

	if err != nil {
		panic(err)
	}

	m, err2 := xml.MarshalIndent(envelope, "", "\t")
	if err2 != nil {
		panic("Well marshalling didn't work: " + err2.Error())
	}
	fmt.Println(envelope.Type())
	fmt.Printf("Marshalled Data: %s\n", m)

}
