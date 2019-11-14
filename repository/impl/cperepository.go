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

	result, err := r.db.Query("SELECT uuid, serial_number, hardware_version FROM cpe WHERE serial_number=? LIMIT 1", serial)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
	}

	for result.Next() {
		cpeInstance := new(cpe.CPE)
		err = result.Scan(&cpeInstance.UUID, &cpeInstance.SerialNumber, &cpeInstance.HardwareVersion)
		if err != nil {
			fmt.Println("Error while fetching query results")
			fmt.Println(err.Error())
		}
		return cpeInstance, nil
	}

	return nil, repository.ErrNotFound
}

func (r *MysqlCPERepositoryImpl) Create(cpe *cpe.CPE) (bool, error) {
	r.db.Ping()

	uuidInstance, _ := uuid.NewRandom()
	uuidString := uuidInstance.String()

	_, err := r.db.Exec(`INSERT INTO cpe SET uuid=?, 
			serial_number=?, 
			hardware_version=?, 
			software_version=?, 
            connection_request_url=?,
			created_at=?, 
			updated_at=?
			`,
		uuidString,
		cpe.SerialNumber,
		cpe.HardwareVersion,
		cpe.SoftwareVersion,
		cpe.ConnectionRequestUrl,
		time.Now(),
		time.Now(),
	)

	if err != nil {
		fmt.Println(err)
		return false, repository.ErrInserting
	}

	cpe.UUID = uuidInstance.String()

	return true, nil
}

func (r *MysqlCPERepositoryImpl) UpdateOrCreate(cpe *cpe.CPE) (result bool, err error) {

	dbCPE, _ := r.FindBySerial(cpe.SerialNumber)

	if dbCPE == nil {
		result, err = r.Create(cpe)
	} else {
		fmt.Println("Updating CPE", cpe)
		stmt, _ := r.db.Prepare(`UPDATE cpe SET 
               hardware_version=?, 
               software_version=?, 
               connection_request_url=?, 
               updated_at=? 
			   WHERE uuid=?`)

		_, err := stmt.Exec(
			cpe.HardwareVersion,
			cpe.SoftwareVersion,
			cpe.ConnectionRequestUrl,
			time.Now(),
			dbCPE.UUID,
		)

		if err != nil {
			return false, repository.ErrUpdating
		}

		result = true
		err = nil

	}

	return result, err
}
