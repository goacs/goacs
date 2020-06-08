package jwt

import (
	"errors"
	"fmt"
	"github.com/dgrijalva/jwt-go"
	"github.com/gin-gonic/gin"
	"strings"
)

func JWTAuthMiddleware(secret string) gin.HandlerFunc {
	return func(c *gin.Context) {
		auth := c.GetHeader("Authorization")
		parts := strings.Fields(auth)

		// Token base validation
		if auth == "" {
			c.AbortWithError(401, errors.New("API token required"))
			return
		}

		// Token base validation
		if strings.ToLower(parts[0]) != "bearer" {
			c.AbortWithError(401, errors.New("Authorization header must start with Bearer"))
			return
		} else if len(parts) == 1 {
			c.AbortWithError(401, errors.New("Token not found"))
			return
		} else if len(parts) > 2 {
			c.AbortWithError(401, errors.New("Authorization header must be Bearer and token"))
			return
		}

		// Parse takes the token string and a function for looking up the key. The latter is especially
		// useful if you use multiple keys for your application.  The standard is to use 'kid' in the
		// head of the token to identify which key to use, but the parsed token (head and claims) is provided
		// to the callback, providing flexibility.
		token, err := jwt.Parse(parts[1], func(token *jwt.Token) (interface{}, error) {
			// Don't forget to validate the alg is what you expect:
			if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
				return nil, fmt.Errorf("Unexpected signing me thod: %v", token.Header["alg"])
			}

			return []byte(secret), nil
		})

		if token.Valid {
			c.Next()
		} else {
			c.AbortWithError(401, err)
			return
		}
	}
}
