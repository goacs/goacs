package http

import (
	"fmt"
	"github.com/gorilla/mux"
	"goacs/acs/logic"
	"net/http"
	"time"
)

var Instance *http.Server

func Start() {
	fmt.Println("Server setup")

	router := mux.NewRouter()

	registerAcsHandler(router)
	RegisterApiRoutes(router)

	http.Handle("/", router)

	Instance = &http.Server{
		Addr:           ":8085",
		ReadTimeout:    5 * time.Second,
		WriteTimeout:   5 * time.Second,
		MaxHeaderBytes: 10000,
	}

	err := Instance.ListenAndServe()
	fmt.Println("Instance started....")

	if err != nil {
		fmt.Println("Unable to start http server")
		return
	}
	fmt.Println("Http server started")
}

func registerAcsHandler(router *mux.Router) {
	router.HandleFunc("/acs", func(respWriter http.ResponseWriter, request *http.Request) {
		defer request.Body.Close()
		logic.CPERequestDecision(request, respWriter)
	})
}
