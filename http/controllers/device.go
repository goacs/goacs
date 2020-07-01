package controllers

import (
	"encoding/json"
	"github.com/gin-gonic/gin"
	"goacs/repository"
	"goacs/repository/impl"
)

func GetDevicesList(ctx *gin.Context) {
	paginatorRequest := repository.PaginatorRequestFromContext(ctx)
	cperepository := impl.NewMysqlCPERepository(repository.GetConnection())
	cpes, total := cperepository.List(paginatorRequest)
	response := repository.NewPaginatorResponse(paginatorRequest, total, cpes)
	json.NewEncoder(ctx.Writer).Encode(response)
}
