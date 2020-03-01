package repository

import (
	"fmt"
	_ "github.com/go-sql-driver/mysql"
	"github.com/jmoiron/sqlx"
	"goacs/lib"
)

var connection *sqlx.DB

func InitConnection() *sqlx.DB {
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

	var err error
	connection, err = sqlx.Open("mysql", connectionString)

	if err != nil {
		panic(err.Error())
	}

	connection.SetMaxOpenConns(20)
	connection.SetMaxIdleConns(20)

	return connection
}

func GetConnection() *sqlx.DB {
	return connection
}
