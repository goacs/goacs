package repository

import (
	"../lib"
	"database/sql"
	"fmt"
	_ "github.com/go-sql-driver/mysql"
)

var connection *sql.DB

func InitConnection() (connection *sql.DB) {
	var env *lib.Env = new(lib.Env)

	fmt.Println("Connecting to database...")
	connectionString := fmt.Sprintf(
		"%s:%s@tcp(%s:%s)/%s?charset=utf8",
		env.Get("MYSQL_USER", ""),
		env.Get("MYSQL_PASSWORD", ""),
		env.Get("MYSQL_HOST", ""),
		env.Get("MYSQL_PORT", "3306"),
		env.Get("MYSQL_DATABASE", ""),
	)

	connection, err := sql.Open("mysql", connectionString)

	if err != nil {
		panic(err.Error())
	}

	return
}

func GetConnection() *sql.DB {
	return connection
}
