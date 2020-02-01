package structs

import (
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestParser(t *testing.T) {
	flag, _ := Parse("RWASP")

	assert.Equal(t, true, flag.Read, "Read flag equal")
	assert.Equal(t, true, flag.Write, "Write flag equal")
	assert.Equal(t, true, flag.System, "System flag equal")
	assert.Equal(t, true, flag.PeriodicRead, "PeriodicRead flag equal")
	assert.Equal(t, true, flag.AddObject, "AddObject flag equal")

}
