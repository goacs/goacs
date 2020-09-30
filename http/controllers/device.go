package controllers

import (
	"encoding/json"
	"github.com/gin-gonic/gin"
	"goacs/acs/types"
	"goacs/models/cpe"
	"goacs/repository"
	"goacs/repository/mysql"
	"log"
	"net/http"
)

type ParameterRequest struct {
	Name  string `json:"name" binding:"required"`
	Value string `json:"value" binding:"required"`
}

func GetDevice(ctx *gin.Context) {
	cperepository := mysql.NewCPERepository(repository.GetConnection())
	cpeModel, err := getCPEFromContext(ctx, cperepository)
	if err == nil {
		json.NewEncoder(ctx.Writer).Encode(cpeModel)
	}
}

func GetDeviceParameters(ctx *gin.Context) {
	paginatorRequest := repository.PaginatorRequestFromContext(ctx)
	cperepository := mysql.NewCPERepository(repository.GetConnection())
	cpeModel, err := getCPEFromContext(ctx, cperepository)
	if err == nil {
		log.Println(paginatorRequest, cpeModel.UUID)
		parameters, total := cperepository.ListCPEParameters(cpeModel, paginatorRequest)
		response := repository.NewPaginatorResponse(paginatorRequest, total, parameters)
		json.NewEncoder(ctx.Writer).Encode(response)
	}
}

func GetDevicesList(ctx *gin.Context) {
	paginatorRequest := repository.PaginatorRequestFromContext(ctx)
	cperepository := mysql.NewCPERepository(repository.GetConnection())
	cpes, total := cperepository.List(paginatorRequest)
	response := repository.NewPaginatorResponse(paginatorRequest, total, cpes)
	json.NewEncoder(ctx.Writer).Encode(response)
}

func UpdateParameter(ctx *gin.Context) {
	cperepository := mysql.NewCPERepository(repository.GetConnection())
	cpeModel, err := getCPEFromContext(ctx, cperepository)
	if err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
	}

	var parameterRequest ParameterRequest
	if err := ctx.ShouldBind(&parameterRequest); err != nil {
		ctx.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
	}

	_, err = cperepository.UpdateParameter(cpeModel, types.ParameterValueStruct{
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

func getCPEFromContext(ctx *gin.Context, cpeRepository mysql.CPERepository) (*cpe.CPE, error) {
	cpeModel, err := cpeRepository.Find(ctx.Param("uuid"))

	if err != nil {
		ctx.AbortWithError(404, err)
		return nil, err
	}

	return cpeModel, nil
}
