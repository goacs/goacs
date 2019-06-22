package acs

import (
	"fmt"
	"math/rand"
	"net/http"
	"strconv"
	"time"
)

const SESSION_LIFETIME = 10

type Session struct {
	id          string
	isNew       bool
	prevReqType string
	created_at  time.Time
}

var sessions map[string]Session

func Init() {
	fmt.Println("sessions init")
	sessions = make(map[string]Session)
	go removeOldSessions()
}
func CreateSession(request *http.Request, w http.ResponseWriter) (*Session, http.ResponseWriter) {
	fmt.Println("### request")
	var sessionId = ""
	cookie, err := request.Cookie("sessionId")

	var session Session

	if err != nil {
		sessionId = generateSessionId()
	} else {
		sessionId = cookie.Value
	}

	fmt.Println("Trying to retive session from memory " + sessionId)
	memorySession, exist := sessions[sessionId]

	if exist == false {
		fmt.Println("session non exist in memory")
		fmt.Println("Creating new session " + session.id)
		session = createEmptySession(sessionId)
	} else {
		session = memorySession
		fmt.Println("session exist in memory")
		session.isNew = false
	}

	newCookie := http.Cookie{Name: "sessionId", Value: sessionId, Expires: time.Now().Add(SESSION_LIFETIME * time.Second)}
	//w.Header().Set("Set-Cookie", "sessionId="+session.id)
	http.SetCookie(w, &newCookie)

	fmt.Println("returning session", session)
	return &session, w
}

func generateSessionId() string {
	rand.NewSource(time.Now().UnixNano())
	return strconv.Itoa(rand.Int())
}

func printSessions() {
	for sessionId, session := range sessions {
		fmt.Println("SessionID " + sessionId + " SessionData: " + strconv.FormatBool(session.isNew))
	}
}

func createEmptySession(sessionId string) Session {
	session := Session{id: sessionId, isNew: true, created_at: time.Now()}
	sessions[sessionId] = session
	return session
}

func removeOldSessions() {
	for {
		now := time.Now()
		for sessionId, session := range sessions {
			if now.Sub(session.created_at).Minutes() > 2 {
				fmt.Println("DELETING OLD SESSION " + sessionId)
				delete(sessions, sessionId)
			}
		}
		time.Sleep(SESSION_LIFETIME * time.Second)
	}
}
