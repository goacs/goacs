package interfaces

import "goacs/models/cpe"

type CPERepository interface {
	Find(uuid string) (*cpe.CPE, error)
	FindBySerial(serial string) (*cpe.CPE, error)
	UpdateOrCreate(cpe *cpe.CPE) (bool, error)
	Create(cpe *cpe.CPE) (bool, error)
}
