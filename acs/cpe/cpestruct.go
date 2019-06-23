package cpe

import "net"

type CPE struct {
	SerialNumber         string
	OUI                  string
	ProductClass         string
	Manufacturer         string
	SoftwareVersion      string
	HardwareVersion      string
	IpAddress            net.IPAddr
	ConnectionRequestUrl string
}
