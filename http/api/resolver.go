package api

import (
	"context"
	"goacs/models/cpe"
	"goacs/repository"
	"goacs/repository/impl"
)

// THIS CODE IS A STARTING POINT ONLY. IT WILL NOT BE UPDATED WITH SCHEMA CHANGES.

type Resolver struct{}

func (r *Resolver) Query() QueryResolver {
	return &queryResolver{r}
}

type queryResolver struct{ *Resolver }

func (r *queryResolver) Cpe(ctx context.Context, uuid *string) (*cpe.CPE, error) {
	cpeRepository := impl.NewMysqlCPERepository(repository.GetConnection())
	return cpeRepository.Find(*uuid)
}

func (r *queryResolver) Cpes(ctx context.Context) ([]*cpe.CPE, error) {
	cpeRepository := impl.NewMysqlCPERepository(repository.GetConnection())
	return cpeRepository.All()
}
