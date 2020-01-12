package main

import (
	"fmt"
	"github.com/joho/godotenv"
	"goacs/acs"
	acshttp "goacs/http"
	"goacs/lib"
	"goacs/repository"
	"os"
	"strconv"
)

var Configuration Config

var Env *lib.Env

type Config struct {
	ACSHttpPort int
}

func init() {
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

func exitApp(msg string, code int) {
	fmt.Println(msg)
	os.Exit(code)
}
