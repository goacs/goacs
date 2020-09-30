package mysql

import (
	"github.com/google/uuid"
	"github.com/jmoiron/sqlx"
	"goacs/models/user"
	"golang.org/x/crypto/bcrypt"
)

type UserRepository struct {
	db *sqlx.DB
}

func NewUserRepository(connection *sqlx.DB) *UserRepository {
	return &UserRepository{
		db: connection,
	}
}

func (r *UserRepository) Find(uuid string) (user.User, error) {
	var userModel user.User
	err := r.db.Get(&userModel, "SELECT * FROM users WHERE uuid=?", &uuid)
	return userModel, err
}

func (r *UserRepository) GetUserByAuthData(username string, password string) (user.User, error) {
	var userModel user.User
	err := r.db.Get(&userModel, "SELECT * FROM users WHERE username=?", &username)

	if err != nil {
		return user.User{}, err
	}

	err = bcrypt.CompareHashAndPassword([]byte(userModel.Password), []byte(password))

	if err != nil {
		return user.User{}, err
	}

	return userModel, nil

}

func (r *UserRepository) CreateUser(userModel *user.User) (user.User, error) {
	uuidInstance, _ := uuid.NewRandom()
	userModel.Uuid = uuidInstance.String()
	userModel.Status = 1
	_, err := r.db.Exec("INSERT INTO users VALUES (?,?,?,?,?)",
		userModel.Uuid, userModel.Username, userModel.Password, userModel.Email, userModel.Status)

	return *userModel, err
}
