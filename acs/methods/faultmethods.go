package methods

import (
	"goacs/acs/http"
	acsxml "goacs/acs/types"
	"goacs/repository/impl"
)

type FaultDecision struct {
	ReqRes *http.ReqRes
}

func (FaultDecision *FaultDecision) ResponseDecision() {
	FaultDecision.ReqRes.Session.PrevReqType = acsxml.FaultResp
	repository := impl.NewFaultRepository()
	repository.SaveFault(&FaultDecision.ReqRes.Session.CPE,
		FaultDecision.ReqRes.Session.CPE.Fault.FaultCode,
		FaultDecision.ReqRes.Session.CPE.Fault.FaultString,
	)
}
