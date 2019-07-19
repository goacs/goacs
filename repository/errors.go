package repository

import "errors"

var (
	ErrNotFound  = errors.New("Requested item not found in database")
	ErrInserting = errors.New("There is some problem while inserting new data")
	ErrUpdating  = errors.New("Error while updating record")
)
