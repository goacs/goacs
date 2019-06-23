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

	session, w := CreateSession(request, w)

	if err != io.EOF && err != nil {
		panic(err)
	}

	reqType, envelope := parseXML(buffer)

	switch reqType {
	case acsxml.INFORM:
		var inform acsxml.Inform
		_ = xml.Unmarshal(buffer, &inform)
		session.PrevReqType = acsxml.INFORM
		session.fillCPEFromInform(inform)
		_, _ = fmt.Fprint(w, envelope.InformResponse())
	case acsxml.EMPTY:
		if session.IsNew == false {
			fmt.Println("GPN REQ")
			_, _ = fmt.Fprint(w, envelope.GPNRequest(""))
			session.PrevReqType = acsxml.EMPTY
		}
	case acsxml.GPNR:
		var gpnr acsxml.GetParameterValuesResponse
		_ = xml.Unmarshal(buffer, &gpnr)
		fmt.Println(gpnr.ParameterList)

	default:
		fmt.Println("NOT SUPPORTED REQTYPE ", reqType)
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
		case "getparameternamesresponse":
			requestType = acsxml.GPNR
		default:
			fmt.Println("NOT SUPPORTED envelope type " + envelope.Type())
			requestType = acsxml.UNKNOWN
		}
	}

	return requestType, envelope
}
