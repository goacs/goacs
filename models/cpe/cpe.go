package cpe

import (
	"../../acs/xml"
	"errors"
	"net"
	"strings"
)

type CPE struct {
	UUID                      string
	SerialNumber              string
	OUI                       string
	ProductClass              string
	Manufacturer              string
	SoftwareVersion           string
	HardwareVersion           string
	IpAddress                 net.IPAddr
	ConnectionRequestUser     string
	ConnectionRequestPassword string
	ConnectionRequestUrl      string
	Root                      string
	ParametersInfo            []xml.ParameterInfo
	ParameterValues           []xml.ParameterValueStruct
}

func (cpe *CPE) AddParameterInfo(parameter xml.ParameterInfo) {
	cpe.ParametersInfo = append(cpe.ParametersInfo, parameter)
}

func (cpe *CPE) AddParametersInfoFromResponse(parameters []xml.ParameterInfo) {
	for _, parameter := range parameters {
		cpe.AddParameterInfo(parameter)
	}
}

func (cpe *CPE) AddParameter(parameter xml.ParameterValueStruct) {
	for index := range cpe.ParameterValues {
		if cpe.ParameterValues[index].Name == parameter.Name {
			//Replace exist parameter
			cpe.ParameterValues[index].Value = parameter.Value
			return
		}
	}
	cpe.ParameterValues = append(cpe.ParameterValues, parameter)
}

func (cpe *CPE) AddParameterValuesFromResponse(parameters []xml.ParameterValueStruct) {
	for _, parameter := range parameters {
		cpe.AddParameter(parameter)
	}
}

func (cpe *CPE) GetParameterValue(parameterName string) (string, error) {
	for index := range cpe.ParameterValues {
		if cpe.ParameterValues[index].Name == parameterName {
			return cpe.ParameterValues[index].Value.Value, nil
		}
	}

	return "", errors.New("Unable to find parameter " + parameterName + " in CPE")
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

func (cpe *CPE) SetRoot(root string) {
	if root == "Device" || root == "InternetGatewayDevice" {
		cpe.Root = root
	}
}

func DetermineDeviceTreeRootPath(parameters []xml.ParameterValueStruct) string {
	for _, parameter := range parameters {
		splittedParamName := strings.Split(parameter.Name, ".")

		if splittedParamName[0] == "Device" {
			return "Device"
		}
	}

	return "InternetGatewayDevice"
}
