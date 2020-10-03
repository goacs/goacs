package logic

import (
	"encoding/xml"
	"fmt"
	"goacs/acs"
	acshttp "goacs/acs/http"
	"goacs/acs/methods"
	acsxml "goacs/acs/types"
	"goacs/lib"
	"goacs/repository"
	"io"
	"io/ioutil"
	"net/http"
)

func CPERequestDecision(request *http.Request, w http.ResponseWriter) {
	env := new(lib.Env)
	buffer, err := ioutil.ReadAll(request.Body)
	session, w := acs.CreateSession(request, w)

	if env.Get("DEBUG", "false") == "true" {
		session.IsBoot = true
	}

	if err != io.EOF && err != nil {
		return
	}

	reqType, envelope := parseEnvelope(buffer, session)

	var reqRes = acshttp.ReqRes{
		Request:      request,
		Response:     w,
		DBConnection: repository.GetConnection(),
		Session:      session,
		Envelope:     envelope,
		Body:         buffer,
	}

	switch reqType {
	case acsxml.INFORM:
		informDecision := methods.InformDecision{&reqRes}
		informDecision.ResponseParser()
		informDecision.Request()

	case acsxml.EMPTY:
		if session.IsNew == false && session.IsBoot == true {
			fmt.Println("GPN REQ")
			parameterDecisions := methods.ParameterDecisions{&reqRes}
			parameterDecisions.ParameterNamesRequest(true)
		}

	case acsxml.GPNResp:
		parameterDecisions := methods.ParameterDecisions{ReqRes: &reqRes}
		parameterDecisions.ParameterNamesResponseParser()

		fmt.Println("GPV REQ")

		//parameterDecisions.GetParameterValuesRequest(reqRes.Session.CPE.ParametersInfo)
		parameterDecisions.GetParameterValuesRequest([]acsxml.ParameterInfo{
			{
				Name:     session.CPE.Root + ".",
				Writable: "0",
			},
		})

	case acsxml.GPVResp:
		parameterDecisions := methods.ParameterDecisions{ReqRes: &reqRes}
		parameterDecisions.GetParameterValuesResponseParser()

	case acsxml.SPVResp:
		paramaterDecisions := methods.ParameterDecisions{ReqRes: &reqRes}
		paramaterDecisions.SetParameterValuesResponse()

	case acsxml.FaultResp:
		var faultresponse acsxml.Fault
		_ = xml.Unmarshal(buffer, &faultresponse)
		session.CPE.Fault = faultresponse
		faultDecision := methods.FaultDecision{ReqRes: &reqRes}
		faultDecision.ResponseDecision()

	default:
		fmt.Println("UNSUPPORTED REQTYPE ", reqType)
	}

	ProcessSessionJobs(&reqRes)

}

func ProcessSessionJobs(reqRes *acshttp.ReqRes) {
	switch reqRes.Session.NextJob {
	case acs.JOB_SENDPARAMETERS:
		parameterDecisions := methods.ParameterDecisions{ReqRes: reqRes}
		parameterDecisions.SetParameterValuesResponse()
	}
}

func parseEnvelope(buffer []byte, session *acs.ACSSession) (string, acsxml.Envelope) {
	//fmt.Println(string(buffer))
	var envelope acsxml.Envelope
	err := xml.Unmarshal(buffer, &envelope)

	if envelope.Header.ID == "" {
		envelope.Header.ID = session.Id
	}

	var requestType string = acsxml.EMPTY

	if err == nil {
		switch envelope.Type() {
		case "inform":
			requestType = acsxml.INFORM
		case "getparameternamesresponse":
			requestType = acsxml.GPNResp
		case "getparametervaluesresponse":
			requestType = acsxml.GPVResp
		case "setparametervaluesresponse":
			requestType = acsxml.SPVResp
		case "fault":
			requestType = acsxml.FaultResp
		default:
			fmt.Println("UNSUPPORTED envelope type " + envelope.Type())
			requestType = acsxml.UNKNOWN
		}
	}

	return requestType, envelope
}
