package main

import (
	"context"
	"fmt"
	"github.com/joho/godotenv"
	"goacs/acs"
	acshttp "goacs/http"
	"goacs/lib"
	"goacs/repository"
	"os"
	"os/signal"
	"strconv"
	"syscall"
	"time"
)

var Configuration Config

var Env *lib.Env

type Config struct {
	ACSHttpPort int
}

func init() {
	listenOnCloseSignal()
	fmt.Println("Initializing app...")
	err := godotenv.Load()

	if err != nil {
		exitApp("Unable to load .env file", 1)
	}

	Env = new(lib.Env)

	Configuration.ACSHttpPort, err = strconv.Atoi(Env.Get("HTTP_PORT", "8085"))

	if err != nil {
		exitApp("Invalid HTTP_PORT", 1)
	}
}

func main() {
	fmt.Println("Starting server...")
	repository.InitConnection()
	acs.StartSession()
	acshttp.Start()
}

func listenOnCloseSignal() {
	c := make(chan os.Signal, 2)
	signal.Notify(c, os.Interrupt, syscall.SIGTERM)
	go func() {
		<-c
		ctx, cancel := context.WithTimeout(context.Background(), time.Second*30)
		defer cancel()

		fmt.Println("Stopping GoACS Server...")

		acshttp.Instance.SetKeepAlivesEnabled(false)
		acshttp.Instance.Shutdown(ctx)

		repository.GetConnection().Close()

		exitApp("GoACS Stopped", 0)
	}()
}

func exitApp(msg string, code int) {
	fmt.Println(msg)
	os.Exit(code)
}
