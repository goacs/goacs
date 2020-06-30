package http

import (
	"fmt"
	"github.com/gin-contrib/cors"
	"github.com/gin-gonic/gin"
	"goacs/acs/logic"
	"goacs/lib"
)

var Instance *gin.Engine

func Start() {
	var env lib.Env
	fmt.Println("Server setup")
	Instance := gin.Default()
	corsConfig := cors.DefaultConfig()
	corsConfig.AllowAllOrigins = true
	corsConfig.AllowCredentials = true
	corsConfig.AllowHeaders = []string{"Origin", "Authorization", "Content-Type", "Accept", "Content-Length"}
	Instance.Use(cors.New(corsConfig))
	registerAcsHandler(Instance)
	RegisterApiRoutes(Instance)

	err := Instance.Run(":" + env.Get("HTTP_PORT", "8085"))
	fmt.Println("Instance started....")

	if err != nil {
		fmt.Println("Unable to start http server")
		return
	}
	fmt.Println("Http server started")
}

func registerAcsHandler(router *gin.Engine) {
	router.GET("/acs", func(ctx *gin.Context) {
		defer ctx.Request.Body.Close()
		logic.CPERequestDecision(ctx.Request, ctx.Writer)
	})

	router.POST("/acs", func(ctx *gin.Context) {
		defer ctx.Request.Body.Close()
		logic.CPERequestDecision(ctx.Request, ctx.Writer)
	})
}
