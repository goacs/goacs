package impl

import (
	"database/sql"
	"fmt"
	"github.com/google/uuid"
	"github.com/jmoiron/sqlx"
	"goacs/acs/types"
	"goacs/models/cpe"
	"goacs/repository"
	"goacs/repository/interfaces"
	"log"
	"time"
)

type MysqlCPERepositoryImpl struct {
	db *sqlx.DB
}

func NewMysqlCPERepository(connection *sqlx.DB) interfaces.CPERepository {
	return &MysqlCPERepositoryImpl{
		db: connection,
	}
}

func (r *MysqlCPERepositoryImpl) All() ([]*cpe.CPE, error) {
	var cpes = []*cpe.CPE{}
	err := r.db.Unsafe().Select(&cpes, "SELECT * FROM cpe")

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound
	}

	return cpes, nil
}

func (r *MysqlCPERepositoryImpl) Find(uuid string) (*cpe.CPE, error) {
	cpeInstance := new(cpe.CPE)
	err := r.db.Unsafe().Get(cpeInstance, "SELECT * FROM cpe WHERE uuid=? LIMIT 1", uuid)

	if err == sql.ErrNoRows {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound
	}

	return cpeInstance, nil
}

func (r *MysqlCPERepositoryImpl) FindBySerial(serial string) (*cpe.CPE, error) {
	cpeInstance := new(cpe.CPE)
	err := r.db.Unsafe().Get(cpeInstance, "SELECT * FROM cpe WHERE serial_number=? LIMIT 1", serial)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound
	}

	return cpeInstance, nil
}

func (r *MysqlCPERepositoryImpl) Create(cpe *cpe.CPE) (bool, error) {
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
			log.Println("error while updatng cpe " + err.Error())
			return false, repository.ErrUpdating
		}

		result = true
		err = nil

	}

	return result, err
}

func (r *MysqlCPERepositoryImpl) FindParameter(cpe *cpe.CPE, parameterKey string) (*types.ParameterValueStruct, error) {
	parameterValueStruct := new(types.ParameterValueStruct)
	err := r.db.Unsafe().Get(&parameterValueStruct, "SELECT *  FROM cpe_parameters WHERE cpe_uuid=? AND name=? LIMIT 1", cpe.UUID, parameterKey)

	if err == sql.ErrNoRows {
		fmt.Printf("Error while fetching query results for cpe %s parameter %s \n", cpe.UUID, parameterKey)
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound
	}

	return parameterValueStruct, nil
}

func (r *MysqlCPERepositoryImpl) CreateParameter(cpe *cpe.CPE, parameter types.ParameterValueStruct) (bool, error) {
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

func (r *MysqlCPERepositoryImpl) UpdateOrCreateParameter(cpe *cpe.CPE, parameter types.ParameterValueStruct) (result bool, err error) {
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

func (r *MysqlCPERepositoryImpl) GetCPEParameters(cpe *cpe.CPE) ([]types.ParameterValueStruct, error) {
	var parameters = []types.ParameterValueStruct{}

	err := r.db.Select(&parameters, "SELECT * FROM cpe_parameters WHERE cpe_uuid=?", cpe.UUID)

	if err != nil {
		return nil, repository.ErrNotFound
	}

	return parameters, nil
}

func (r *MysqlCPERepositoryImpl) LoadParameters(cpe *cpe.CPE) (bool, error) {
	var err error
	cpe.ParameterValues, err = r.GetCPEParameters(cpe)

	return err == nil, err
}
