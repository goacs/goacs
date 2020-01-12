package interfaces

import "goacs/models/templates"

type TemplateRepository interface {
	Find(id int64) (*templates.Template, error)
	FindByName(serial string) (*templates.Template, error)
	GetParametersForTemplate(template_id int64) ([]templates.TemplateParameter, error)
}
