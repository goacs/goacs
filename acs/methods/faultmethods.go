package methods

import (
	"goacs/acs/http"
	acsxml "goacs/acs/types"
	"goacs/repository/mysql"
)

type FaultDecision struct {
	ReqRes *http.ReqRes
}

func (FaultDecision *FaultDecision) ResponseDecision() {
	FaultDecision.ReqRes.Session.PrevReqType = acsxml.FaultResp
	repository := mysql.NewFaultRepository()
	repository.SaveFault(&FaultDecision.ReqRes.Session.CPE,
		FaultDecision.ReqRes.Session.CPE.Fault.FaultCode,
		FaultDecision.ReqRes.Session.CPE.Fault.FaultString,
	)
}
