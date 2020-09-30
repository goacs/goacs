package methods

import (
	"encoding/xml"
	"fmt"
	"goacs/acs/http"
	acsxml "goacs/acs/types"
	"goacs/repository/mysql"
)

type InformDecision struct {
	ReqRes *http.ReqRes
}

func (InformDecision *InformDecision) Request() {
	InformDecision.ReqRes.Session.PrevReqType = acsxml.INFORM
	_, _ = fmt.Fprint(InformDecision.ReqRes.Response, InformDecision.ReqRes.Envelope.InformResponse())
}

func (InformDecision *InformDecision) ResponseParser() {
	var inform acsxml.Inform
	_ = xml.Unmarshal(InformDecision.ReqRes.Body, &inform)
	fmt.Println("BOOT", inform.IsBootEvent())
	InformDecision.ReqRes.Session.FillCPEFromInform(inform)
	cpeRepository := mysql.NewCPERepository(InformDecision.ReqRes.DBConnection)
	_, _ = cpeRepository.UpdateOrCreate(&InformDecision.ReqRes.Session.CPE)
	_, _ = cpeRepository.SaveParameters(&InformDecision.ReqRes.Session.CPE)
}
