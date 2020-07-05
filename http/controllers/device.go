package controllers

import (
	"encoding/json"
	"github.com/gin-gonic/gin"
	"goacs/acs/types"
	"goacs/models/cpe"
	"goacs/repository"
	"goacs/repository/impl"
	"goacs/repository/interfaces"
	"log"
	"net/http"
)

type ParameterRequest struct {
	Name  string `json:"name" binding:"required"`
	Value string `json:"value" binding:"required"`
}

func GetDevice(ctx *gin.Context) {
	cperepository := impl.NewMysqlCPERepository(repository.GetConnection())
	cpe, err := getCPEFromContext(ctx, cperepository)
	if err == nil {
		json.NewEncoder(ctx.Writer).Encode(cpe)
	}
}

func GetDeviceParameters(ctx *gin.Context) {
	paginatorRequest := repository.PaginatorRequestFromContext(ctx)
	cperepository := impl.NewMysqlCPERepository(repository.GetConnection())
	cpe, err := getCPEFromContext(ctx, cperepository)
	if err == nil {
		log.Println(paginatorRequest, cpe.UUID)
		parameters, total := cperepository.ListCPEParameters(cpe, paginatorRequest)
		response := repository.NewPaginatorResponse(paginatorRequest, total, parameters)
		json.NewEncoder(ctx.Writer).Encode(response)
	}
}

func GetDevicesList(ctx *gin.Context) {
	paginatorRequest := repository.PaginatorRequestFromContext(ctx)
	cperepository := impl.NewMysqlCPERepository(repository.GetConnection())
	cpes, total := cperepository.List(paginatorRequest)
	response := repository.NewPaginatorResponse(paginatorRequest, total, cpes)
	json.NewEncoder(ctx.Writer).Encode(response)
}

func UpdateParameter(ctx *gin.Context) {
	cperepository := impl.NewMysqlCPERepository(repository.GetConnection())
	cpe, err := getCPEFromContext(ctx, cperepository)
	if err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
	}

	var parameterRequest ParameterRequest
	if err := ctx.ShouldBind(&parameterRequest); err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
	}

	_, err = cperepository.UpdateParameter(cpe, types.ParameterValueStruct{
		Name:  parameterRequest.Name,
		Value: parameterRequest.Value,
	})

	if err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
	}

	ctx.JSON(204, "")
}

func CreateParameter(ctx *gin.Context) {

}

func getCPEFromContext(ctx *gin.Context, cpeRepository interfaces.CPERepository) (*cpe.CPE, error) {
	cpe, err := cpeRepository.Find(ctx.Param("uuid"))

	if err != nil {
		ctx.AbortWithError(404, err)
		return nil, err
	}

	return cpe, nil
}
