package types

import (
	"database/sql/driver"
	"errors"
	"log"
	"net"
	"reflect"
)

type IPAddress net.IPAddr

func (i IPAddress) Value() (driver.Value, error) {
	return i.IP.String(), nil
}

func (i *IPAddress) Scan(src interface{}) (err error) {
	log.Println(reflect.TypeOf(src).String())

	switch src.(type) {
	case []uint8:
		src := src.([]byte)
		i.IP = net.ParseIP(string(src))
		err = nil
	default:
		err = errors.New("Invalid address")
	}

	return
}
