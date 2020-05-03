package controllers

import (
	"encoding/json"
	"github.com/dgrijalva/jwt-go"
	"goacs/lib"
	"goacs/models/user"
	"goacs/repository"
	"goacs/repository/impl"
	"log"
	"net/http"
)

type LoginRequest struct {
	Username string `json:"username"`
	Password string `json:"password"`
}

type LoginResponse struct {
	User  user.User `json:"user"`
	Token string    `json:"token"`
}

func Login(w http.ResponseWriter, r *http.Request) {
	var request LoginRequest
	err := json.NewDecoder(r.Body).Decode(&request)

	if err != nil {
		log.Println("Error in req ", err)
	}

	userRepository := impl.NewUserRepository(repository.GetConnection())
	user, err := userRepository.GetUserByAuthData(request.Username, request.Password)

	if err != nil {
		log.Println("Cannot find user")
		return
	}

	loginResponse := LoginResponse{
		User:  user,
		Token: NewTokenForUser(user),
	}

	json.NewEncoder(w).Encode(loginResponse)
}

func NewTokenForUser(user user.User) string {
	env := new(lib.Env)
	token := jwt.NewWithClaims(jwt.SigningMethodHS256, jwt.StandardClaims{
		ExpiresAt: 15000,
		Subject:   user.Uuid,
		Issuer:    "user",
	})

	tokenString, err := token.SignedString([]byte(env.Get("JWT_SECRET", "")))
	if err != nil {
		log.Println("Error while generating token ", err)
	}
	return tokenString
}
