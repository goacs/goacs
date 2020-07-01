package main

import (
	"fmt"
	"github.com/joho/godotenv"
	"goacs/acs"
	acshttp "goacs/http"
	"goacs/lib"
	"goacs/repository"
	"os"
	"runtime"
	"strconv"
	"time"
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
	go PrintMemUsage()

	acshttp.Start()

}

func exitApp(msg string, code int) {
	fmt.Println(msg)
	os.Exit(code)
}

func PrintMemUsage() {
	for {
		var m runtime.MemStats
		runtime.ReadMemStats(&m)
		// For info on each, see: https://golang.org/pkg/runtime/#MemStats
		fmt.Printf("Alloc = %v MiB", bToMb(m.Alloc))
		fmt.Printf("\tTotalAlloc = %v MiB", bToMb(m.TotalAlloc))
		fmt.Printf("\tSys = %v MiB", bToMb(m.Sys))
		fmt.Printf("\tNumGC = %v\n", m.NumGC)
		time.Sleep(time.Second * 5)
	}
}

func bToMb(b uint64) uint64 {
	return b / 1024 / 1024
}
