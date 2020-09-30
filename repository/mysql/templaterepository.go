package mysql

import (
	"fmt"
	"github.com/jmoiron/sqlx"
	"goacs/models/templates"
	"goacs/repository"
)

type TemplateRepository struct {
	db *sqlx.DB
}

func NewTemplateRepository(connection *sqlx.DB) TemplateRepository {
	return TemplateRepository{
		db: connection,
	}
}

func (r TemplateRepository) Find(id int64) (*templates.Template, error) {
	templateInstance := new(templates.Template)
	err := r.db.Unsafe().Get(templateInstance, "SELECT * FROM templates WHERE id=? LIMIT 1", id)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound

	}

	return templateInstance, nil
}

func (r TemplateRepository) FindByName(name string) (*templates.Template, error) {
	templateInstance := new(templates.Template)

	err := r.db.Unsafe().Get(templateInstance, "SELECT id, name FROM templates WHERE name=? LIMIT 1", name)

	if err != nil {
		fmt.Println("Error while fetching query results")
		fmt.Println(err.Error())
		return nil, repository.ErrNotFound

	}

	return templateInstance, nil
}

func (r TemplateRepository) GetParametersForTemplate(template_id int64) ([]templates.TemplateParameter, error) {
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
