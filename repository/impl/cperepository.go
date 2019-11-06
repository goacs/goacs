package impl

import (
	".."
	"../../models/cpe"
	"../interfaces"
	"database/sql"
	"fmt"
	"github.com/google/uuid"
	"time"
)

type MysqlCPERepositoryImpl struct {
	db *sql.DB
}

func NewMysqlCPERepository(connection *sql.DB) interfaces.CPERepository {
	return &MysqlCPERepositoryImpl{
		db: connection,
	}
}

func (r *MysqlCPERepositoryImpl) Find(uuid string) (*cpe.CPE, error) {

	return &cpe.CPE{}, nil
}

func (r *MysqlCPERepositoryImpl) FindBySerial(serial string) (*cpe.CPE, error) {
	r.db.Ping()

	result, err := r.db.Query("SELECT id, serial_number, vendor, hwversion FROM cpe WHERE serial_number=? LIMIT 1", serial)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
	}

	for result.Next() {
		fmt.Println("blebleble")
		cpeInstance := new(cpe.CPE)
		_ = result.Scan(&cpeInstance.UUID, &cpeInstance.SerialNumber, &cpeInstance.Manufacturer, &cpeInstance.HardwareVersion)
		return cpeInstance, nil
	}

	return nil, repository.ErrNotFound
}

func (r *MysqlCPERepositoryImpl) Create(cpe *cpe.CPE) (bool, error) {
	r.db.Ping()

	uuidInstance, _ := uuid.NewRandom()
	uuidString := uuidInstance.String()

	_, err := r.db.Exec("INSERT INTO cpe SET id=?, serial_number=?, hwversion=?, vendor=?, created_at=?", uuidString, cpe.SerialNumber, cpe.HardwareVersion, cpe.Manufacturer, time.Now())

	if err != nil {
		fmt.Println(err)
		return false, repository.ErrInserting
	}

	cpe.UUID = uuidInstance.String()

	return true, nil
}

func (r *MysqlCPERepositoryImpl) UpdateOrCreate(cpe *cpe.CPE) (result bool, err error) {

	existInDb, _ := r.FindBySerial(cpe.SerialNumber)

	fmt.Println(cpe)

	if existInDb == nil {
		result, err = r.Create(cpe)
	} else {
		stmt, _ := r.db.Prepare("UPDATE cpe SET hwversion=?, vendor=? WHERE id=?")

		_, err := stmt.Exec(cpe.HardwareVersion, cpe.Manufacturer, cpe.UUID)

		if err != nil {
			return false, repository.ErrUpdating
		}

		result = true
		err = nil

	}

	return result, err
}
