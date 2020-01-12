package impl

import (
	"database/sql"
	"fmt"
	"goacs/models/templates"
	"goacs/repository"
	"goacs/repository/interfaces"
)

type MysqlTemplateRepositoryImpl struct {
	db *sql.DB
}

func NewMysqlTemplateRepository(connection *sql.DB) interfaces.TemplateRepository {
	return &MysqlTemplateRepositoryImpl{
		db: connection,
	}
}

func (r MysqlTemplateRepositoryImpl) Find(id int64) (*templates.Template, error) {
	r.db.Ping()

	result, err := r.db.Query("SELECT id, name FROM templates WHERE id=? LIMIT 1", id)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound

	}

	for result.Next() {
		templateInstance := new(templates.Template)
		err = result.Scan(&templateInstance.Id, &templateInstance.Name)
		if err != nil {
			fmt.Println("Error while fetching query results")
			fmt.Println(err.Error())
		}
		return templateInstance, nil
	}

	return nil, repository.ErrNotFound
}

func (r MysqlTemplateRepositoryImpl) FindByName(name string) (*templates.Template, error) {
	r.db.Ping()

	result, err := r.db.Query("SELECT id, name FROM templates WHERE name=? LIMIT 1", name)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound

	}

	for result.Next() {
		templateInstance := new(templates.Template)
		err = result.Scan(&templateInstance.Id, &templateInstance.Name)
		if err != nil {
			fmt.Println("Error while fetching query results")
			fmt.Println(err.Error())
		}
		return templateInstance, nil
	}

	return nil, repository.ErrNotFound
}

func (r MysqlTemplateRepositoryImpl) GetParametersForTemplate(template_id int64) ([]templates.TemplateParameter, error) {
	r.db.Ping()

	result, err := r.db.Query(
		"SELECT template_id, name, value, type, flags FROM templates_parameters WHERE template_id=? LIMIT 1",
		template_id,
	)

	if err != nil {
		return nil, repository.ErrNotFound
	}

	var parameters []templates.TemplateParameter

	for result.Next() {
		var parameterInstance templates.TemplateParameter
		err = result.Scan(
			&parameterInstance.TemplateId,
			&parameterInstance.Name,
			&parameterInstance.Value,
			&parameterInstance.Type,
			&parameterInstance.Flags,
		)
		if err != nil {
			fmt.Println("Error while fetching query results")
			fmt.Println(err.Error())
			return nil, repository.ErrNotFound
		}

		parameters = append(parameters, parameterInstance)
	}

	return parameters, nil
}
