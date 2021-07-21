#!/usr/bin/env bash

docker-compose up node
docker-compose exec acs composer install --no-dev
docker-compose exec acs php artisan migrate
docker-compose exec acs php artisan db:seed
docker-compose exec acs supervisorctl restart server
