package logic

import (
	".."
	"../../repository"
	acshttp "../http"
	"../methods"
	acsxml "../xml"
	"encoding/xml"
	"fmt"
	"io"
	"io/ioutil"
	"net/http"
)

func CPERequestDecision(request *http.Request, w http.ResponseWriter) {
	buffer, err := ioutil.ReadAll(request.Body)

	session, w := acs.CreateSession(request, w)

	if err != io.EOF && err != nil {
		panic(err)
	}

	reqType, envelope := parseXML(buffer)

	var reqRes = acshttp.ReqRes{
		Request:      request,
		Response:     w,
		DBConnection: repository.GetConnection(),
		Session:      session,
		Envelope:     envelope,
	}

	fmt.Println(reqRes)

	baseDecision := methods.Decision{reqRes}

	switch reqType {
	case acsxml.INFORM:
		decision := methods.InformDecision{baseDecision}
		decision.RequestParser()
		decision.Response()

	case acsxml.EMPTY:
		if session.IsNew == false && session.IsBoot == true {
			fmt.Println("GPN REQ")
			_, _ = fmt.Fprint(w, envelope.GPNRequest(""))
			session.PrevReqType = acsxml.GPNReq
		}

	case acsxml.GPNResp:
		var gpnr acsxml.GetParameterNamesResponse
		_ = xml.Unmarshal(buffer, &gpnr)
		session.CPE.AddParametersInfoFromResponse(gpnr.ParameterList)

		fmt.Println("GPV REQ")
		requestBody := envelope.GPVRequest([]acsxml.ParameterInfo{
			{
				Name:     session.CPE.Root + ".",
				Writable: "0",
			},
		})
		_, _ = fmt.Fprint(w, requestBody)
		session.PrevReqType = acsxml.GPVReq

	case acsxml.GPVResp:
		var gpvr acsxml.GetParameterValuesResponse
		_ = xml.Unmarshal(buffer, &gpvr)
		session.CPE.AddParameterValuesFromResponse(gpvr.ParameterList)
		//fmt.Println(session.CPE.ParameterValues)

	default:
		fmt.Println("UNSUPPORTED REQTYPE ", reqType)
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
			requestType = acsxml.GPNResp
		case "getparametervaluesresponse":
			requestType = acsxml.GPVResp
		default:
			fmt.Println("UNSUPPORTED envelope type " + envelope.Type())
			requestType = acsxml.UNKNOWN
		}
	}

	return requestType, envelope
}
