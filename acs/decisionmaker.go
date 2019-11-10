package acs

import (
	acsxml "../acs/xml"
	"../models/cpe"
	"../repository"
	"../repository/impl"
	"encoding/xml"
	"fmt"
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

	cpeRepository := impl.NewMysqlCPERepository(repository.InitConnection())

	switch reqType {
	case acsxml.INFORM:
		var inform acsxml.Inform
		_ = xml.Unmarshal(buffer, &inform)
		fmt.Println("BOOT", inform.IsBootEvent())
		session.PrevReqType = acsxml.INFORM
		session.fillCPEFromInform(inform)
		fmt.Println(session.cpe)
		_, _ = cpeRepository.UpdateOrCreate(&session.cpe)
		_, _ = fmt.Fprint(w, envelope.InformResponse())

	case acsxml.EMPTY:
		if session.IsNew == false && session.IsBoot == true {
			fmt.Println("GPN REQ")
			_, _ = fmt.Fprint(w, envelope.GPNRequest(""))
			session.PrevReqType = acsxml.GPNReq
		}

	case acsxml.GPNResp:
		var gpnr acsxml.GetParameterNamesResponse
		_ = xml.Unmarshal(buffer, &gpnr)
		session.cpe.AddParametersInfoFromResponse(gpnr.ParameterList)
		session.cpe.Root = cpe.DetermineDeviceTreeRootPath(gpnr.ParameterList)

		fmt.Println("GPV REQ")
		requestBody := envelope.GPVRequest([]acsxml.ParameterInfo{
			{
				Name:     session.cpe.Root + ".",
				Writable: "0",
			},
		})
		_, _ = fmt.Fprint(w, requestBody)
		session.PrevReqType = acsxml.GPVReq

	case acsxml.GPVResp:
		var gpvr acsxml.GetParameterValuesResponse
		_ = xml.Unmarshal(buffer, &gpvr)
		session.cpe.AddParameterValuesFromResponse(gpvr.ParameterList)
		fmt.Println(session.cpe.ParameterValues)

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
