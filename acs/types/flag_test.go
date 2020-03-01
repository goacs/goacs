package types

import (
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestParser(t *testing.T) {
	flag, _ := Parse("RWASP")

	assert.Equal(t, true, flag.Read, "Read flag is false")
	assert.Equal(t, true, flag.Write, "Write flag is false")
	assert.Equal(t, true, flag.System, "System flag is false")
	assert.Equal(t, true, flag.PeriodicRead, "PeriodicRead flag is false")
	assert.Equal(t, true, flag.AddObject, "AddObject flag is false")

}

func TestBadValue(t *testing.T) {
	_, err := Parse("C")

	assert.Error(t, err, "Bad flag not returns error")
}
