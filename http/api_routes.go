package http

import (
	"github.com/gorilla/mux"
	"goacs/http/controllers"
)

func RegisterApiRoutes(router *mux.Router) {
	router = router.PathPrefix("/api").Subrouter()
	router.HandleFunc("/login", controllers.Login)
	router.HandleFunc("/user/create", controllers.UserCreate)
}
