package repository

import (
	"database/sql"
	_ "github.com/go-sql-driver/mysql"
)

func DatabaseConnection() (db *sql.DB) {
	db, err := sql.Open("mysql", "root:admin1@tcp(localhost:3306)/acs?charset=utf8")

	if err != nil {
		panic(err.Error())
	}

	return
}
