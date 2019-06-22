package http

import (
	"fmt"
	"goacs/acs"
	"net/http"
	"time"
)

var Instance *http.Server

func Start() {
	fmt.Println("Server setup")
	Instance = &http.Server{
		Addr:           ":8085",
		ReadTimeout:    5 * time.Second,
		WriteTimeout:   5 * time.Second,
		MaxHeaderBytes: 10000,
	}

	registerAcsHandler()

	err := Instance.ListenAndServe()
	fmt.Println("Instance started....")

	if err != nil {
		fmt.Println("Unable to start http server")
		return
	}
	fmt.Println("Http server started")
}

func registerAcsHandler() {
	http.HandleFunc("/acs", func(respWriter http.ResponseWriter, request *http.Request) {
		defer request.Body.Close()
		acs.MakeDecision(request, respWriter)
	})
}
