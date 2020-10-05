package mysql

import (
	"github.com/jmoiron/sqlx"
	"goacs/models/tasks"
)

type TasksRepository struct {
	db *sqlx.DB
}

func NewTasksRepository(connection *sqlx.DB) TasksRepository {
	return TasksRepository{
		db: connection,
	}
}

func (t *TasksRepository) GetTasksForCPE(cpe_uuid string) []tasks.Task {
	var cpeTasks []tasks.Task
	_ = t.db.Select(&cpeTasks, "SELECT * FROM tasks WHERE cpe_uuid=? AND done_at is null AND not_before >= now()", cpe_uuid)

	return cpeTasks
}
