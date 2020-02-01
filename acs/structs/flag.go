package structs

type Flag struct {
	Read         bool //R
	Write        bool //W
	AddObject    bool //A
	System       bool //S
	PeriodicRead bool //P
}

func Parse(flags string) (Flag, error) {
	var flag Flag = Flag{
		Read:         false,
		Write:        false,
		AddObject:    false,
		System:       false,
		PeriodicRead: false,
	}

	for _, token := range flags {
		switch token {
		case 'R':
			flag.Read = true
		case 'W':
			flag.Write = true
		case 'A':
			flag.AddObject = true
		case 'S':
			flag.System = true
		case 'P':
			flag.PeriodicRead = true
		default:
			panic("Unknown flag " + string(token))
		}
	}

	return flag, nil
}
