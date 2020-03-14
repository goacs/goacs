package fault

import "time"

type Fault struct {
	UUID      string    `db:"uuid"`
	CPEUUID   string    `db:"cpe_uuid"`
	Code      string    `db:"code"`
	Message   string    `db:"message"`
	CreatedAt time.Time `db:"created_at"`
}
