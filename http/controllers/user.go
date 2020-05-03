package controllers

import (
	"encoding/json"
	"goacs/models/user"
	"goacs/repository"
	"goacs/repository/impl"
	"log"
	"net/http"
)

type UserCreateRequest struct {
	Username string `json:"username"`
	Password string `json:"password"`
	Email    string `json:"email"`
}

func UserCreate(w http.ResponseWriter, r *http.Request) {
	var request UserCreateRequest
	err := json.NewDecoder(r.Body).Decode(&request)
	if err != nil {
		log.Println("Error in req")
	}

	userModel := user.User{
		Username: request.Username,
		Password: user.EncryptPassword(request.Password),
		Email:    request.Email,
	}

	userRepository := impl.NewUserRepository(repository.GetConnection())
	user, err := userRepository.CreateUser(&userModel)
	log.Print(userModel, user)
	json.NewEncoder(w).Encode(user)
}
