package main

import (
	"./http"
	"fmt"
	"goacs/acs"
)

var Debug bool = true

func main() {
	fmt.Println("Starting app...")
	acs.Init()

	http.Start()
}
func IsDebug() bool {
	return Debug
}
