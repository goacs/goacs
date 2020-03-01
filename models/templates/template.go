package templates

import (
	"goacs/acs/types"
)

type Template struct {
	Id         int64  `db:"id"`
	Name       string `db:"name"`
	Parameters []TemplateParameter
}

type TemplateParameter struct {
	TemplateId int64 `db:"template_id"`
	types.ParameterValueStruct
}

func (own *TemplateParameter) CompareTemplates(other []TemplateParameter) {
	//TODO: dorobić, pomyslec go-cmp

}
