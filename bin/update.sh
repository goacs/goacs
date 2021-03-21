#!/usr/bin/env bash

docker-compose up node
docker-composer exec acs composer install --no-dev
docker-composer exec acs php artisan migrate
docker-composer exec acs php artisan db:seed
docker-composer exec acs supervisorctl restart goacs
