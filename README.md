## About GoACS
GoACS is an Autonomous Configuration Server which implements TR-069 protocol.
Feel free to contribute to project

### Development
Current code are still in active development and are not usable!

If you want to look what is server doing, do following steps 

Copy `.env.example` to `.env`

Edit `.env` file if you want to change something. Default values works fine

Run mysql dev server using **docker-compose**

`docker-compose up goacs-db`

then

`go run main.go`

