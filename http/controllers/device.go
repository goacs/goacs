package controllers

import (
	"encoding/json"
	"github.com/gin-gonic/gin"
	"goacs/repository"
	"goacs/repository/impl"
)

func GetDevice(ctx *gin.Context) {
	cperepository := impl.NewMysqlCPERepository(repository.GetConnection())
	cpe, err := cperepository.Find(ctx.Param("uuid"))

	if err != nil {
		ctx.AbortWithError(404, err)
	}

	json.NewEncoder(ctx.Writer).Encode(cpe)
}

func GetDevicesList(ctx *gin.Context) {
	paginatorRequest := repository.PaginatorRequestFromContext(ctx)
	cperepository := impl.NewMysqlCPERepository(repository.GetConnection())
	cpes, total := cperepository.List(paginatorRequest)
	response := repository.NewPaginatorResponse(paginatorRequest, total, cpes)
	json.NewEncoder(ctx.Writer).Encode(response)
}
