package interfaces

import "goacs/models/cpe"
import "goacs/acs/types"

type CPERepository interface {
	All() ([]*cpe.CPE, error)
	Find(uuid string) (*cpe.CPE, error)
	FindBySerial(serial string) (*cpe.CPE, error)
	UpdateOrCreate(cpe *cpe.CPE) (bool, error)
	Create(cpe *cpe.CPE) (bool, error)
	SaveParameters(cpe *cpe.CPE) (bool, error)
	LoadParameters(cpe *cpe.CPE) (bool, error)
	FindParameter(cpe *cpe.CPE, parameterKey string) (*types.ParameterValueStruct, error)
	CreateParameter(cpe *cpe.CPE, parameter types.ParameterValueStruct) (bool, error)
	UpdateOrCreateParameter(cpe *cpe.CPE, parameter types.ParameterValueStruct) (result bool, err error)
}
