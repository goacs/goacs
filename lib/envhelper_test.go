package lib

import (
	"os"
	"testing"
)

func TestEnv_Get(t *testing.T) {
	_ = os.Setenv("SYSTEM_TEST", "4321")

	env := new(Env)

	got := env.Get("SYSTEM_TEST", "1234")

	if got != "4321" {
		t.Fail()
	}
}

func TestEnv_GetDefault(t *testing.T) {
	env := new(Env)

	got := env.Get("TEST", "1234")

	if got != "1234" {
		t.Fail()
	}
}
