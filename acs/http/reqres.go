package http

import (
	"github.com/jmoiron/sqlx"
	"goacs/acs"
	acsxml "goacs/acs/types"
	"net/http"
)

type ReqRes struct {
	Request      *http.Request
	Response     http.ResponseWriter
	DBConnection *sqlx.DB
	Session      *acs.ACSSession
	Envelope     acsxml.Envelope
	Body         []byte
}
