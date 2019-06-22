package acs

import (
	"fmt"
	"math/rand"
	"net/http"
	"strconv"
	"strings"
	"time"
)

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
}
func CreateSession(request *http.Request, w http.ResponseWriter) (*Session, http.ResponseWriter) {
	fmt.Println("### request")
	//printSessions()
	sessionId := request.Header.Get("Cookie")

	var session Session

	if sessionId == "" {
		sessionId = generateSessionId()
	} else {
		sessionId = extractSessionId(sessionId)
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

	w.Header().Set("Set-Cookie", "sessionId="+session.id)

	fmt.Println("returning session", session)
	return &session, w
}

func generateSessionId() string {
	rand.NewSource(time.Now().UnixNano())
	return strconv.Itoa(rand.Int())
}

func extractSessionId(sessionId string) string {
	split := strings.Split(sessionId, "=")
	return split[1]
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
