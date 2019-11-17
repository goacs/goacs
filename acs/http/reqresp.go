package http

import (
	".."
	acsxml "../xml"
	"database/sql"
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
