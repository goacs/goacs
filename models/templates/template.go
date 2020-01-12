package templates

import (
	"goacs/acs/xml"
)

type Template struct {
	Id         int64
	Name       string
	Parameters []TemplateParameter
}

type TemplateParameter struct {
	TemplateId int64
	xml.ParameterValueStruct
	Type  string
	Flags string
}

func (own *TemplateParameter) CompareTemplates(other []TemplateParameter) {
	//TODO: dorobić, pomyslec go-cmp

}
