package impl

import (
	"database/sql"
	"fmt"
	"github.com/google/uuid"
	"goacs/acs/xml"
	"goacs/models/cpe"
	"goacs/repository"
	"goacs/repository/interfaces"
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

func (r *MysqlCPERepositoryImpl) All() ([]*cpe.CPE, error) {
	r.db.Ping()

	result, err := r.db.Query("SELECT uuid, serial_number, hardware_version FROM cpe")

	var cpes []*cpe.CPE

	if err == nil {
		for result.Next() {
			cpeInstance := new(cpe.CPE)
			_ = result.Scan(&cpeInstance.UUID, &cpeInstance.SerialNumber, &cpeInstance.HardwareVersion)
			cpes = append(cpes, cpeInstance)
		}
	}

	return cpes, nil
}

func (r *MysqlCPERepositoryImpl) Find(uuid string) (*cpe.CPE, error) {
	r.db.Ping()

	result := r.db.QueryRow("SELECT uuid, serial_number, hardware_version FROM cpe WHERE uuid=? LIMIT 1", uuid)

	cpeInstance := new(cpe.CPE)
	err := result.Scan(&cpeInstance.UUID, &cpeInstance.SerialNumber, &cpeInstance.HardwareVersion)
	if err == sql.ErrNoRows {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound
	}

	return cpeInstance, nil
}

func (r *MysqlCPERepositoryImpl) FindBySerial(serial string) (*cpe.CPE, error) {
	r.db.Ping()

	result := r.db.QueryRow("SELECT uuid, serial_number, hardware_version FROM cpe WHERE serial_number=? LIMIT 1", serial)

	cpeInstance := new(cpe.CPE)
	err := result.Scan(&cpeInstance.UUID, &cpeInstance.SerialNumber, &cpeInstance.HardwareVersion)
	if err == sql.ErrNoRows {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound
	}

	return cpeInstance, nil
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
      connection_request_user=?,
      connection_request_password=?,              
			created_at=?, 
			updated_at=?
			`,
		uuidString,
		cpe.SerialNumber,
		cpe.HardwareVersion,
		cpe.SoftwareVersion,
		cpe.ConnectionRequestUrl,
		cpe.ConnectionRequestUser,
		cpe.ConnectionRequestPassword,
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
		fmt.Println("Updating CPE")
		stmt, _ := r.db.Prepare(`UPDATE cpe SET 
               hardware_version=?, 
               software_version=?, 
               connection_request_url=?, 
               connection_request_user=?,
      			   connection_request_password=?,       
               updated_at=? 
			   WHERE uuid=?`)

		_, err := stmt.Exec(
			cpe.HardwareVersion,
			cpe.SoftwareVersion,
			cpe.ConnectionRequestUrl,
			cpe.ConnectionRequestUser,
			cpe.ConnectionRequestPassword,
			time.Now(),
			dbCPE.UUID,
		)
		cpe.UUID = dbCPE.UUID

		if err != nil {
			return false, repository.ErrUpdating
		}

		result = true
		err = nil

	}

	return result, err
}

func (r *MysqlCPERepositoryImpl) FindParameter(cpe *cpe.CPE, parameterKey string) (*xml.ParameterValueStruct, error) {
	result := r.db.QueryRow("SELECT name, value, type  FROM cpe_parameters WHERE cpe_uuid=? AND name=? LIMIT 1", cpe.UUID, parameterKey)

	parameterValueStruct := new(xml.ParameterValueStruct)
	err := result.Scan(&parameterValueStruct.Name, &parameterValueStruct.Value.Value, &parameterValueStruct.Value.Type)

	if err == sql.ErrNoRows {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound
	}

	return parameterValueStruct, nil
}

func (r *MysqlCPERepositoryImpl) CreateParameter(cpe *cpe.CPE, parameter xml.ParameterValueStruct) (bool, error) {
	var query string = `INSERT INTO cpe_parameters (cpe_uuid, name, value, type, flags, created_at, updated_at) 
						VALUES (?, ?, ?, ?, ?, ?, ?)`

	stmt, _ := r.db.Prepare(query)

	_, err := stmt.Exec(
		cpe.UUID,
		parameter.Name,
		parameter.Value.Value,
		parameter.Value.Type, //TODO: NORMALIZE
		"",                   //TODO: Flags support (R - Read, W - Write and more...)
		time.Now(),
		time.Now(),
	)

	if err != nil {
		fmt.Println(repository.ErrParameterCreating, err.Error())
		return false, err
	}

	return true, nil
}

func (r *MysqlCPERepositoryImpl) UpdateOrCreateParameter(cpe *cpe.CPE, parameter xml.ParameterValueStruct) (result bool, err error) {
	existParameter, err := r.FindParameter(cpe, parameter.Name)

	if existParameter == nil {
		//fmt.Println("non exist param", existParameter)
		result, err = r.CreateParameter(cpe, parameter)
	} else {
		//fmt.Println("param exist", existParameter)
		var query string = "UPDATE cpe_parameters SET value=?, type=?, flags=?, updated_at=? WHERE cpe_uuid=? and name = ?"
		stmt, _ := r.db.Prepare(query)

		_, err = stmt.Exec(
			parameter.Value.Value,
			parameter.Value.Type,
			"",
			time.Now(),
			cpe.UUID,
			parameter.Name,
		)

		if err != nil {
			fmt.Println("ERROR", err.Error())
			result = false
		}
	}

	return
}

func (r *MysqlCPERepositoryImpl) SaveParameters(cpe *cpe.CPE) (bool, error) {

	for _, parameterValue := range cpe.ParameterValues {
		//fmt.Println("param value", parameterValue)
		_, err := r.UpdateOrCreateParameter(cpe, parameterValue)

		if err != nil {
			fmt.Println(repository.ErrParameterCreating, err.Error())
			return false, err
		}
	}

	return true, nil
}

func (r *MysqlCPERepositoryImpl) LoadParameters(cpe *cpe.CPE) (bool, error) {
	panic("implement me")
}
