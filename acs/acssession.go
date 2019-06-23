package acs

import (
	"fmt"
	"goacs/acs/cpe"
	"goacs/acs/xml"
	"math/rand"
	"net/http"
	"strconv"
	"time"
)

const SESSION_LIFETIME = 15
const SESSION_GOROUTINE_TIMEOUT = 10

type ACSSession struct {
	Id          string
	IsNew       bool
	PrevReqType string
	created_at  time.Time
	cpe         cpe.CPE
}

var acsSessions map[string]ACSSession

func StartSession() {
	fmt.Println("acsSessions init")
	acsSessions = make(map[string]ACSSession)
	go removeOldSessions()
}
func CreateSession(request *http.Request, w http.ResponseWriter) (*ACSSession, http.ResponseWriter) {
	fmt.Println("### request")
	var sessionId = ""
	cookie, err := request.Cookie("sessionId")

	var session ACSSession

	if err != nil {
		sessionId = generateSessionId()
	} else {
		sessionId = cookie.Value
	}

	fmt.Println("Trying to retive session from memory " + sessionId)
	memorySession, exist := acsSessions[sessionId]

	if exist == false {
		fmt.Println("session non exist in memory")
		fmt.Println("Creating new session " + session.Id)
		session = createEmptySession(sessionId)
	} else {
		session = memorySession
		fmt.Println("session exist in memory")
		session.IsNew = false
	}

	newCookie := http.Cookie{Name: "sessionId", Value: sessionId, Expires: time.Now().Add(SESSION_LIFETIME * time.Second)}
	//w.Header().Set("Set-Cookie", "sessionId="+session.Id)
	http.SetCookie(w, &newCookie)

	fmt.Println("returning session", session)
	return &session, w
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

func createEmptySession(sessionId string) ACSSession {
	session := ACSSession{Id: sessionId, IsNew: true, created_at: time.Now()}
	acsSessions[sessionId] = session
	return session
}

func removeOldSessions() {
	for {
		now := time.Now()
		for sessionId, session := range acsSessions {
			if now.Sub(session.created_at).Minutes() > SESSION_LIFETIME {
				fmt.Println("DELETING OLD SESSION " + sessionId)
				delete(acsSessions, sessionId)
			}
		}
		time.Sleep(SESSION_GOROUTINE_TIMEOUT * time.Second)
	}
}

func (session *ACSSession) fillCPEFromInform(inform xml.Inform) {
	session.cpe = cpe.CPE{}
}
