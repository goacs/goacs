package http

import (
	"database/sql"
	"goacs/acs"
	acsxml "goacs/acs/structs"
	"net/http"
)

type ReqRes struct {
	Request      *http.Request
	Response     http.ResponseWriter
	DBConnection *sql.DB
	Session      *acs.ACSSession
	Envelope     acsxml.Envelope
	Body         []byte
}
