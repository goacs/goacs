package methods

import (
	acsxml "../../acs/xml"
	"../http"
	"encoding/xml"
	"fmt"
)

type ParameterDecisions struct {
	ReqRes *http.ReqRes
}

func (pd *ParameterDecisions) ParameterNamesRequest() {
	pd.ReqRes.Session.PrevReqType = acsxml.GPNReq
	_, _ = fmt.Fprint(pd.ReqRes.Response, pd.ReqRes.Envelope.GPNRequest(pd.ReqRes.Session.CPE.Root))

}

func (pd *ParameterDecisions) ParamereNamesResponseParser() {
	var gpnr acsxml.GetParameterNamesResponse
	_ = xml.Unmarshal(pd.ReqRes.Body, &gpnr)
	pd.ReqRes.Session.CPE.AddParametersInfoFromResponse(gpnr.ParameterList)

	fmt.Println(gpnr.ParameterList)
}
