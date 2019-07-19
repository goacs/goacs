package cpe

import (
	"goacs/acs/xml"
	"net"
	"strings"
)

type CPE struct {
	UUID                 string
	SerialNumber         string
	OUI                  string
	ProductClass         string
	Manufacturer         string
	SoftwareVersion      string
	HardwareVersion      string
	IpAddress            net.IPAddr
	ConnectionRequestUrl string
	Root                 string
	ParametersInfo       []xml.ParameterInfo
	ParameterValues      []xml.ParameterValue
}

func (cpe *CPE) AddParameterInfo(parameter xml.ParameterInfo) {
	cpe.ParametersInfo = append(cpe.ParametersInfo, parameter)
}

func (cpe *CPE) AddParametersInfoFromResponse(parameters []xml.ParameterInfo) {
	for _, parameter := range parameters {
		cpe.AddParameterInfo(parameter)
	}
}

func (cpe *CPE) AddParameter(parameter xml.ParameterValue) {
	cpe.ParameterValues = append(cpe.ParameterValues, parameter)
}

func (cpe *CPE) AddParameterValuesFromResponse(parameters []xml.ParameterValue) {
	for _, parameter := range parameters {
		cpe.AddParameter(parameter)
	}
}

func (cpe *CPE) GetFullPathParameterNames() []xml.ParameterInfo {
	var filteredParameters []xml.ParameterInfo
	for _, parameter := range cpe.ParametersInfo {
		//check if last char in Name is not equal to . (dot)
		if parameter.Name[len(parameter.Name)-1:] != "." {
			filteredParameters = append(filteredParameters, parameter)
		}
	}

	return filteredParameters
}

func DetermineDeviceTreeRootPath(parameters []xml.ParameterInfo) string {
	for _, parameter := range parameters {
		splittedParamName := strings.Split(parameter.Name, ".")

		if splittedParamName[0] == "Device" { //
			return "Device"
		}
	}

	return "InternetGatewayDevice"
}
