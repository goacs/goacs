package lib

import "os"

type Env struct {
}

func (env *Env) Get(key string, def string) (ret string) {
	ret = os.Getenv(key)
	if ret == "" {
		return def
	}
	return
}
