package methods

import (
	"encoding/xml"
	"fmt"
	"goacs/acs"
	"goacs/acs/http"
	acsxml "goacs/acs/types"
	"goacs/repository"
	"goacs/repository/mysql"
	"log"
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
	//fmt.Println(request)
	_, _ = fmt.Fprint(pd.ReqRes.Response, request)

}

func (pd *ParameterDecisions) ParameterNamesResponseParser() {
	var gpnr acsxml.GetParameterNamesResponse
	log.Println("ParameterNamesResponseParser")

	//log.Println(string(pd.ReqRes.Body))
	_ = xml.Unmarshal(pd.ReqRes.Body, &gpnr)
	pd.ReqRes.Session.CPE.AddParametersInfo(gpnr.ParameterList)

	//fmt.Println(gpnr.ParameterList)
}

func (pd *ParameterDecisions) GetParameterValuesRequest(parameters []acsxml.ParameterInfo) {
	var request = pd.ReqRes.Envelope.GPVRequest(parameters)
	_, _ = fmt.Fprint(pd.ReqRes.Response, request)
	pd.ReqRes.Session.PrevReqType = acsxml.GPVReq

}

func (pd *ParameterDecisions) GetParameterValuesResponseParser() {
	var gpvr acsxml.GetParameterValuesResponse
	_ = xml.Unmarshal(pd.ReqRes.Body, &gpvr)
	log.Println("GetParameterValuesResponseParser")
	//log.Println(string(pd.ReqRes.Body))

	pd.ReqRes.Session.CPE.AddParameterValues(gpvr.ParameterList)
	cpeRepository := mysql.NewCPERepository(repository.GetConnection())
	dbParameters, err := cpeRepository.GetCPEParameters(&pd.ReqRes.Session.CPE)

	if err != nil {
		log.Println("Error GetParameterValuesResponseParser ", err.Error())
	}

	if len(dbParameters) > 0 {
		//Get modified parameters
		//Check for AddObject instances
		diffParameters := pd.ReqRes.Session.CPE.GetChangedParametersToWrite(&dbParameters)
		pd.ReqRes.Session.CPE.AddParameterValues(diffParameters)
		pd.ReqRes.Session.NextJob = acs.JOB_SENDPARAMETERS
	}

	_ = cpeRepository.BulkInsertOrUpdateParameters(&pd.ReqRes.Session.CPE, pd.ReqRes.Session.CPE.ParameterValues)

}

func (pd *ParameterDecisions) SetParameterValuesResponse() {
	parametersToWrite := pd.ReqRes.Session.CPE.GetParametersWithFlag("W")
	log.Println("parametersToWrite")
	//log.Println(parametersToWrite)

	//TODO: Check why some parameters are writeable, but cpe returns fault on it
	if len(parametersToWrite) > 0 {
		var response = pd.ReqRes.Envelope.SetParameterValues(parametersToWrite)
		_, _ = fmt.Fprint(pd.ReqRes.Response, response)
		pd.ReqRes.Session.PrevReqType = acsxml.SPVResp
	}
}
