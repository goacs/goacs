package user

import (
	"golang.org/x/crypto/bcrypt"
	"log"
)

type User struct {
	Uuid     string `json:"uuid" db:"uuid"`
	Username string `json:"username" db:"username"`
	Password string `json:"-" db:"password"`
	Email    string `json:"email" db:"email"`
	Status   int    `json:"status" db:"status"`
}

func EncryptPassword(password string) string {
	hashed, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)

	if err != nil {
		log.Println("Cannot hash password :(")
	}

	return string(hashed)
}
