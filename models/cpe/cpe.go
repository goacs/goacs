package cpe

import (
	"errors"
	"goacs/acs/types"
	"log"
	"reflect"
	"strings"
	"time"
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
	UUID                      string          `json:"uuid" db:"uuid"`
	SerialNumber              string          `json:"serial_number" db:"serial_number"`
	OUI                       string          `json:"oui" db:"oui"`
	ProductClass              string          `json:"product_class" db:"product_class"`
	Manufacturer              string          `json:"manufacturer" db:"manufacturer"`
	SoftwareVersion           string          `json:"software_version" db:"software_version"`
	HardwareVersion           string          `json:"hardware_version" db:"hardware_version"`
	IpAddress                 types.IPAddress `json:"ip_address" db:"ip_address"`
	ConnectionRequestUser     string          `json:"connection_request_user" db:"connection_request_user"`
	ConnectionRequestPassword string          `json:"connection_request_password" db:"connection_request_password"`
	ConnectionRequestUrl      string          `json:"connection_request_url" db:"connection_request_url"`
	Root                      string
	ParametersInfo            []types.ParameterInfo
	ParameterValues           []types.ParameterValueStruct
	Fault                     types.Fault
	UpdatedAt                 time.Time `json:"updated_at" db:"updated_at"`
}

type ParametersDiff struct {
	AddedParameters    []types.ParameterValueStruct
	ModifiedParameters []types.ParameterValueStruct
	RemovedParameters  []types.ParameterValueStruct
}

func (cpe *CPE) AddParameterInfo(parameter types.ParameterInfo) {
	cpe.ParametersInfo = append(cpe.ParametersInfo, parameter)
}

func (cpe *CPE) AddParametersInfo(parameters []types.ParameterInfo) {
	for _, parameter := range parameters {
		cpe.AddParameterInfo(parameter)
		//TODO: Apply bool conversion
		//cpe.UpdateParameterFlags(parameter.Name, types.Flag{
		//	Read:  true,
		//	Write: parameter.Writable == "1",
		//})
	}
}

func (cpe *CPE) GetParameterInfoByName(name string) (types.ParameterInfo, error) {
	for _, parameter := range cpe.ParametersInfo {
		if parameter.Name == name {
			return parameter, nil
		}
	}

	return types.ParameterInfo{}, errors.New("Cannot find parameter")
}

func (cpe *CPE) UpdateParameterFlags(parameterName string, flag types.Flag) {
	for index := range cpe.ParameterValues {
		if cpe.ParameterValues[index].Name == parameterName {
			//Replace exist parameter
			cpe.ParameterValues[index].Flag = flag
			return
		}
	}
}

func (cpe *CPE) AddParameter(parameter types.ParameterValueStruct) {
	for index := range cpe.ParameterValues {
		if cpe.ParameterValues[index].Name == parameter.Name {
			//Replace exist parameter
			cpe.ParameterValues[index].Value = parameter.Value

			parameterInfo, err := cpe.GetParameterInfoByName(parameter.Name)

			if err == nil {
				log.Println(parameter.Name)
				log.Println(parameterInfo.Writable)
				parameter.Flag.Write = parameterInfo.Writable == "1"
			}

			cpe.ParameterValues[index].Flag = parameter.Flag

			return
		}
	}
	cpe.ParameterValues = append(cpe.ParameterValues, parameter)
}

func (cpe *CPE) AddParameterValues(parameters []types.ParameterValueStruct) {
	for _, parameter := range parameters {
		parameter.Flag.Read = true
		cpe.AddParameter(parameter)
	}
}

func (cpe *CPE) GetParameterValue(parameterName string) (string, error) {
	for index := range cpe.ParameterValues {
		if cpe.ParameterValues[index].Name == parameterName {
			return cpe.ParameterValues[index].Value, nil
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

func (cpe *CPE) GetParametersWithFlag(flag string) []types.ParameterValueStruct {
	parameters := []types.ParameterValueStruct{}
	for _, parameter := range cpe.ParameterValues {
		fieldName := parameter.Flag.CharToFieldName(flag)
		flagBool := reflect.ValueOf(parameter.Flag).FieldByName(fieldName).Bool()
		if flagBool == true {
			parameters = append(parameters, parameter)
		}
	}
	return parameters
}

func (cpe *CPE) SetRoot(root string) {
	if root == "Device" || root == "InternetGatewayDevice" {
		cpe.Root = root
	}
}

func (cpe *CPE) Fails() bool {
	return cpe.Fault.FaultCode != "" || cpe.Fault.FaultString != ""
}

func (cpe *CPE) GetChangedParametersToWrite(otherParameters *[]types.ParameterValueStruct) []types.ParameterValueStruct {
	parametersDiff := []types.ParameterValueStruct{}

	for _, cpeParam := range cpe.ParameterValues {
		for _, otherParam := range *otherParameters {
			if otherParam.Flag.Write && otherParam.Name == cpeParam.Name && otherParam.Value != cpeParam.Value {
				parametersDiff = append(parametersDiff, otherParam)
				break
			}
		}
	}

	return parametersDiff
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
