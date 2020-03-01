package methods

import (
	"encoding/xml"
	"fmt"
	"goacs/acs/http"
	acsxml "goacs/acs/types"
)

type ParameterDecisions struct {
	ReqRes *http.ReqRes
}

func (pd *ParameterDecisions) ParameterNamesRequest(recursively bool) {
	pd.ReqRes.Session.PrevReqType = acsxml.GPNReq
	root := pd.ReqRes.Session.CPE.Root
	if recursively {
		root = root + "."
	}
	var request = pd.ReqRes.Envelope.GPNRequest(root)
	fmt.Println(request)
	_, _ = fmt.Fprint(pd.ReqRes.Response, request)

}

func (pd *ParameterDecisions) ParameterNamesResponseParser() {
	var gpnr acsxml.GetParameterNamesResponse
	_ = xml.Unmarshal(pd.ReqRes.Body, &gpnr)
	pd.ReqRes.Session.CPE.AddParametersInfoFromResponse(gpnr.ParameterList)

	//fmt.Println(gpnr.ParameterList)
}

func (pd *ParameterDecisions) ParameterValuesRequest(parameters []acsxml.ParameterInfo) {
	var request = pd.ReqRes.Envelope.GPVRequest(parameters)
	_, _ = fmt.Fprint(pd.ReqRes.Response, request)
	pd.ReqRes.Session.PrevReqType = acsxml.GPVReq

}

func (pd *ParameterDecisions) ParameterValuesResponseParser() {
	var gpvr acsxml.GetParameterValuesResponse
	_ = xml.Unmarshal(pd.ReqRes.Body, &gpvr)
	pd.ReqRes.Session.CPE.AddParameterValuesFromResponse(gpvr.ParameterList)

}
