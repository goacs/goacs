package methods

import (
	"encoding/xml"
	"fmt"
	"goacs/acs/http"
	acsxml "goacs/acs/structs"
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

func (pd *ParameterDecisions) ParameterValuesRequest() {

}
