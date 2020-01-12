package interfaces

import "goacs/models/cpe"
import "goacs/acs/xml"

type CPERepository interface {
	All() ([]*cpe.CPE, error)
	Find(uuid string) (*cpe.CPE, error)
	FindBySerial(serial string) (*cpe.CPE, error)
	UpdateOrCreate(cpe *cpe.CPE) (bool, error)
	Create(cpe *cpe.CPE) (bool, error)
	SaveParameters(cpe *cpe.CPE) (bool, error)
	LoadParameters(cpe *cpe.CPE) (bool, error)
	FindParameter(cpe *cpe.CPE, parameterKey string) (*xml.ParameterValueStruct, error)
	CreateParameter(cpe *cpe.CPE, parameter xml.ParameterValueStruct) (bool, error)
	UpdateOrCreateParameter(cpe *cpe.CPE, parameter xml.ParameterValueStruct) (result bool, err error)
}
