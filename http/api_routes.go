package http

import (
	"github.com/gin-gonic/gin"
	"goacs/http/controllers"
	"goacs/http/middleware/jwt"
	"goacs/lib"
)

func RegisterApiRoutes(gin *gin.Engine) {
	var env lib.Env
	apiGroup := gin.Group("/api")
	apiGroup.POST("/auth/login", controllers.Login)

	apiGroup.Use(jwt.JWTAuthMiddleware(env.Get("JWT_SECRET", "")))
	{
		apiGroup.POST("/user/create", controllers.UserCreate)
	}
}
