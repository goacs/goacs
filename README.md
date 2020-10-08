## About GoACS
GoACS is an Autonomous Configuration Server which implements TR-069 protocol.
Feel free to contribute to project

## Sponsors
##### [MULTIPLAY](https://multiplay.pl)
![GRUPA MULTIPLAY](.github/sponsors/mpl_logo.png "GRUPA MULTIPLAY") [![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fgoacs%2Fgoacs.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fgoacs%2Fgoacs?ref=badge_shield)


## Development
Current code are still in active development and are not usable!

If you want to look what is server doing, do following steps 

Copy `.env.example` to `.env`

Edit `.env` file if you want to change something. Default values works fine

Run mysql dev server using **docker-compose**

`docker-compose up goacs-db`

then

`go run main.go`
 
 


## License
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fgoacs%2Fgoacs.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fgoacs%2Fgoacs?ref=badge_large)