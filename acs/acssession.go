package acs

import (
	"fmt"
	"goacs/acs/types"
	"goacs/models/cpe"
	"math/rand"
	"net/http"
	"strconv"
	"sync"
	"time"
)

const SessionLifetime = 15
const SessionGoroutineTimeout = 10

const (
	JOB_SENDPARAMETERS = 1
)

type ACSSession struct {
	Id          string
	IsNew       bool
	IsBoot      bool
	PrevReqType string
	created_at  time.Time
	CPE         cpe.CPE
	NextJob     int
}

var lock = sync.RWMutex{}
var acsSessions map[string]*ACSSession

func StartSession() {
	fmt.Println("acsSessions init")
	acsSessions = make(map[string]*ACSSession)
	go removeOldSessions()
}

func CreateSession(request *http.Request, w http.ResponseWriter) (*ACSSession, http.ResponseWriter) {
	fmt.Println("### request")
	var sessionId = ""
	cookie, err := request.Cookie("sessionId")

	var session *ACSSession

	if err != nil {
		sessionId = generateSessionId()
	} else {
		sessionId = cookie.Value
	}

	fmt.Println("Trying to retive session from memory " + sessionId)
	lock.RLock()
	_, exist := acsSessions[sessionId]
	lock.RUnlock()

	if exist == false {
		fmt.Println("session non exist in memory")
		fmt.Println("Creating new session " + sessionId)
		session = createEmptySession(sessionId)
	} else {
		session = acsSessions[sessionId]
		fmt.Println("session exist in memory")
		session.IsNew = false
	}

	newCookie := http.Cookie{Name: "sessionId", Value: sessionId, Expires: time.Now().Add(SessionLifetime * time.Second)}
	http.SetCookie(w, &newCookie)

	return session, w
}

func generateSessionId() string {
	rand.NewSource(time.Now().UnixNano())
	return strconv.Itoa(rand.Int())
}

func printSessions() {
	for sessionId, session := range acsSessions {
		fmt.Println("SessionID " + sessionId + " SessionData: " + strconv.FormatBool(session.IsNew))
	}
}

func createEmptySession(sessionId string) *ACSSession {
	session := ACSSession{Id: sessionId, IsNew: true, created_at: time.Now()}
	lock.Lock()
	acsSessions[sessionId] = &session
	lock.Unlock()
	return acsSessions[sessionId]
}

func removeOldSessions() {
	for {
		now := time.Now()
		for sessionId, session := range acsSessions {
			if now.Sub(session.created_at).Minutes() > SessionLifetime {
				fmt.Println("DELETING OLD SESSION " + sessionId)
				lock.Lock()
				delete(acsSessions, sessionId)
				lock.Unlock()
			}
		}
		time.Sleep(SessionGoroutineTimeout * time.Second)
	}
}

func (session *ACSSession) FillCPEFromInform(inform types.Inform) {
	session.CPE = cpe.CPE{
		Manufacturer:    inform.DeviceId.Manufacturer,
		SerialNumber:    inform.DeviceId.SerialNumber,
		OUI:             inform.DeviceId.OUI,
		HardwareVersion: "1.0",
	}
	session.CPE.AddParameterValues(inform.ParameterList)
	session.CPE.SetRoot(cpe.DetermineDeviceTreeRootPath(session.CPE.ParameterValues))
	session.CPE.ConnectionRequestUrl, _ = session.CPE.GetParameterValue(session.CPE.Root + ".ManagementServer.ConnectionRequestURL")
	session.CPE.ConnectionRequestUser, _ = session.CPE.GetParameterValue(session.CPE.Root + ".ManagementServer.Username")
	session.CPE.ConnectionRequestUser, _ = session.CPE.GetParameterValue(session.CPE.Root + ".ManagementServer.Password")
	session.IsBoot = inform.IsBootEvent()
	fmt.Println(session.CPE)
}
