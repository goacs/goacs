module goacs

go 1.13

require (
	github.com/dgrijalva/jwt-go v3.2.0+incompatible
	github.com/go-sql-driver/mysql v1.5.0
	github.com/google/uuid v1.1.1
	github.com/gorilla/mux v1.7.4
	github.com/jmoiron/sqlx v1.2.0
	github.com/joho/godotenv v1.3.0
	github.com/stretchr/testify v1.3.0
	golang.org/x/crypto v0.0.0-20200429183012-4b2356b1ed79
)

replace goacs => ./
