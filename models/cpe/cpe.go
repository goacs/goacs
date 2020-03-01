package cpe

import (
	"errors"
	"goacs/acs/types"
	"strings"
)

/**
create table cpe
(
	uuid varchar(36) not null
		primary key,
	serial_number varchar(50) null,
	oui varchar(6) null,
	software_version varchar(20) null,
	hardware_version varchar(20) null,
	ip_address varchar(15) null,
	connection_request_url varchar(255) null,
	connection_request_user varchar(50) null,
	connection_request_password varchar(64) null,
	created_at datetime default current_timestamp() not null,
	updated_at datetime default current_timestamp() not null
);

create index cpe_serial_number_index
	on cpe (serial_number);


*/
type CPE struct {
	UUID                      string `db:"uuid"`
	SerialNumber              string `db:"serial_number"`
	OUI                       string `db:"oui"`
	ProductClass              string
	Manufacturer              string
	SoftwareVersion           string          `db:"software_version"`
	HardwareVersion           string          `db:"hardware_version"`
	IpAddress                 types.IPAddress `db:"ip_address"`
	ConnectionRequestUser     string          `db:"connection_request_user"`
	ConnectionRequestPassword string          `db:"connection_request_password"`
	ConnectionRequestUrl      string          `db:"connection_request_url"`
	Root                      string
	ParametersInfo            []types.ParameterInfo
	ParameterValues           []types.ParameterValueStruct
	Fault                     types.Fault
}

func (cpe *CPE) AddParameterInfo(parameter types.ParameterInfo) {
	cpe.ParametersInfo = append(cpe.ParametersInfo, parameter)
}

func (cpe *CPE) AddParametersInfoFromResponse(parameters []types.ParameterInfo) {
	for _, parameter := range parameters {
		cpe.AddParameterInfo(parameter)
	}
}

func (cpe *CPE) AddParameter(parameter types.ParameterValueStruct) {
	for index := range cpe.ParameterValues {
		if cpe.ParameterValues[index].Name == parameter.Name {
			//Replace exist parameter
			cpe.ParameterValues[index].Value = parameter.Value
			return
		}
	}
	cpe.ParameterValues = append(cpe.ParameterValues, parameter)
}

func (cpe *CPE) AddParameterValuesFromResponse(parameters []types.ParameterValueStruct) {
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

func (cpe *CPE) GetFullPathParameterNames() []types.ParameterInfo {
	var filteredParameters []types.ParameterInfo
	for _, parameter := range cpe.ParametersInfo {
		//check if last char in Name is not equal to . (dot)
		if parameter.Name[len(parameter.Name)-1:] != "." {
			filteredParameters = append(filteredParameters, parameter)
		}
	}

	return filteredParameters
}

func (cpe *CPE) GetParametersToWrite() []types.ParameterValueStruct {
	//TODO
	return []types.ParameterValueStruct{}
}

func (cpe *CPE) SetRoot(root string) {
	if root == "Device" || root == "InternetGatewayDevice" {
		cpe.Root = root
	}
}

func (cpe *CPE) Fails() bool {
	return cpe.Fault.FaultCode != "" || cpe.Fault.FaultString != ""
}

func DetermineDeviceTreeRootPath(parameters []types.ParameterValueStruct) string {
	for _, parameter := range parameters {
		splittedParamName := strings.Split(parameter.Name, ".")

		if splittedParamName[0] == "Device" {
			return "Device"
		}
	}

	return "InternetGatewayDevice"
}
