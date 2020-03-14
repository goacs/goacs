package impl

import (
	"github.com/google/uuid"
	"github.com/jmoiron/sqlx"
	"goacs/models/cpe"
	"goacs/repository"
	"time"
)

type FaultRepository struct {
	db *sqlx.DB
}

func NewFaultRepository() *FaultRepository {
	return &FaultRepository{
		db: repository.GetConnection(),
	}
}

func SaveFault(cpe *cpe.CPE, code string, message string) {
	repository := NewFaultRepository()
	repository.SaveFault(cpe, code, message)
}

func (repository *FaultRepository) SaveFault(cpe *cpe.CPE, code string, message string) {
	uuidInstance, _ := uuid.NewRandom()
	uuidString := uuidInstance.String()

	repository.db.Exec("INSERT INTO faults VALUES (?,?,?,?,?)",
		uuidString,
		cpe.UUID,
		code,
		message,
		time.Now())

}
