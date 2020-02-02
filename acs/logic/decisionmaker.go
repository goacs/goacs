package logic

import (
	"encoding/xml"
	"fmt"
	"goacs/acs"
	acshttp "goacs/acs/http"
	"goacs/acs/methods"
	acsxml "goacs/acs/structs"
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
		panic(err)
	}

	reqType, envelope := parseXML(buffer)

	var reqRes = acshttp.ReqRes{
		Request:      request,
		Response:     w,
		DBConnection: repository.GetConnection(),
		Session:      session,
		Envelope:     envelope,
		Body:         buffer,
	}

	fmt.Println("ENVELOPE TYPE", reqRes.Envelope.Type())

	switch reqType {
	case acsxml.INFORM:
		decision := methods.InformDecision{&reqRes}
		decision.ResponseParser()
		decision.Request()

	case acsxml.EMPTY:
		if session.IsNew == false && session.IsBoot == true {
			fmt.Println("GPN REQ")
			decision := methods.ParameterDecisions{&reqRes}
			decision.ParameterNamesRequest(true)
		}

	case acsxml.GPNResp:
		decision := methods.ParameterDecisions{ReqRes: &reqRes}
		decision.ParameterNamesResponseParser()

		fmt.Println("GPV REQ")

		decision.ParameterValuesRequest([]acsxml.ParameterInfo{
			{
				Name:     session.CPE.Root + ".",
				Writable: "0",
			},
		})

	case acsxml.GPVResp:
		decision := methods.ParameterDecisions{ReqRes: &reqRes}
		decision.ParameterValuesResponseParser()

	case acsxml.FaultResp:
		var faultresponse acsxml.Fault
		_ = xml.Unmarshal(buffer, &faultresponse)
		session.CPE.Fault = faultresponse
		//TODO: zapis do DB i zakończenie sesji

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
		case "fault":
			requestType = acsxml.FaultResp
		default:
			fmt.Println("UNSUPPORTED envelope type " + envelope.Type())
			requestType = acsxml.UNKNOWN
		}
	}

	return requestType, envelope
}
