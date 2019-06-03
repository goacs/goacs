package acs

import (
	"encoding/xml"
	"fmt"
	acsxml "goacs/acs/xml"
	"io"
	"io/ioutil"
	"net/http"
)

func MakeDecision(request *http.Request, w http.ResponseWriter) {

	buffer, err := ioutil.ReadAll(request.Body)

	if err != io.EOF && err != nil {
		panic(err)
	}

	fmt.Println(string(buffer))

	reqType, envelope := parseXML(buffer)

	if reqType == acsxml.INFORM {
		var inform acsxml.Inform
		_ = xml.Unmarshal(buffer, &inform)

	}

}

func parseXML(buffer []byte) (string, acsxml.Envelope) {
	var envelope acsxml.Envelope
	err := xml.Unmarshal(buffer, &envelope)

	var requestType string = acsxml.EMPTY

	if err == nil {
		switch envelope.Type() {
		case "inform":
			requestType = acsxml.INFORM
		default:
			requestType = acsxml.UNKNOWN
		}
	}

	return requestType, envelope
}
