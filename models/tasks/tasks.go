package tasks

import "time"

type Task struct {
	Id        int64     `json:"id" db:"id"`
	CpeUuid   string    `json:"cpe_uuid" db:"cpe_uuid"`
	Event     string    `json:"event" db:"event"`
	NotBefore time.Time `json:"not_before" db:"not_before"`
	Script    string    `json:"script" db:"script"`
	CreatedAt time.Time `json:"created_at" db:"created_at"`
	DoneAt    time.Time `json:"done_at" db:"done_at"`
}

func FilterTasksByEvent(event string, tasksList []Task) []Task {
	var filteredTasks []Task
	for _, task := range tasksList {
		if task.Event == event {
			filteredTasks = append(filteredTasks, task)
		}
	}

	return filteredTasks
}
