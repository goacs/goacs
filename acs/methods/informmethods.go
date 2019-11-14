package methods

import (
	acsxml "../../acs/xml"
	"../../repository/impl"
	"encoding/xml"
	"fmt"
	"io/ioutil"
)

type InformDecision struct {
	Decision
}

func (InformDecision *InformDecision) Response() {
	_, _ = fmt.Fprint(InformDecision.ReqRes.Response, InformDecision.ReqRes.Envelope.InformResponse())
}

func (InformDecision *InformDecision) RequestParser() {
	buffer, err := ioutil.ReadAll(InformDecision.ReqRes.Request.Body)
	//TODO: DON'T KNOW WHY BUFFER IS EMPTY :(
	fmt.Println(buffer, err)
	var inform acsxml.Inform
	_ = xml.Unmarshal(buffer, &inform)
	fmt.Println("BOOT", inform.IsBootEvent())
	InformDecision.ReqRes.Session.PrevReqType = acsxml.INFORM
	InformDecision.ReqRes.Session.FillCPEFromInform(inform)
	cpeRepository := impl.NewMysqlCPERepository(InformDecision.ReqRes.DBConnection)
	_, _ = cpeRepository.UpdateOrCreate(&InformDecision.ReqRes.Session.CPE)
}
