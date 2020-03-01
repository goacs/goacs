package impl

import (
	"fmt"
	"github.com/jmoiron/sqlx"
	"goacs/models/templates"
	"goacs/repository"
	"goacs/repository/interfaces"
)

type MysqlTemplateRepositoryImpl struct {
	db *sqlx.DB
}

func NewMysqlTemplateRepository(connection *sqlx.DB) interfaces.TemplateRepository {
	return &MysqlTemplateRepositoryImpl{
		db: connection,
	}
}

func (r MysqlTemplateRepositoryImpl) Find(id int64) (*templates.Template, error) {
	templateInstance := new(templates.Template)
	err := r.db.Unsafe().Get(templateInstance, "SELECT * FROM templates WHERE id=? LIMIT 1", id)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound

	}

	return templateInstance, nil
}

func (r MysqlTemplateRepositoryImpl) FindByName(name string) (*templates.Template, error) {
	templateInstance := new(templates.Template)

	err := r.db.Unsafe().Get(templateInstance, "SELECT id, name FROM templates WHERE name=? LIMIT 1", name)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound

	}

	return templateInstance, nil
}

func (r MysqlTemplateRepositoryImpl) GetParametersForTemplate(template_id int64) ([]templates.TemplateParameter, error) {
	var parameters = []templates.TemplateParameter{}

	err := r.db.Unsafe().Select(&parameters,
		"SELECT * FROM templates_parameters WHERE template_id=? LIMIT 1",
		template_id,
	)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound
	}

	return parameters, nil
}
