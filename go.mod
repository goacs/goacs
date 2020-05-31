module goacs

go 1.13

require (
	github.com/davecgh/go-spew v1.1.1 // indirect
	github.com/dgrijalva/jwt-go v3.2.0+incompatible
	github.com/go-sql-driver/mysql v1.5.0
	github.com/google/uuid v1.1.1
	github.com/gorilla/mux v1.7.4
	github.com/jmoiron/sqlx v1.2.0
	github.com/joho/godotenv v1.3.0
	github.com/kr/pretty v0.1.0 // indirect
	github.com/stretchr/testify v1.4.0
	golang.org/x/crypto v0.0.0-20200429183012-4b2356b1ed79
	gopkg.in/check.v1 v1.0.0-20190902080502-41f04d3bba15 // indirect
	gopkg.in/yaml.v2 v2.2.4 // indirect
)

replace goacs => ./
