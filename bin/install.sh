#!/usr/bin/env bash

docker-compose up node
docker-compose up -d goacs
docker-composer exec acs composer install --no-dev
docker-composer exec acs php artisan key:generate
docker-composer exec acs php artisan jwt:secret --force
docker-composer exec acs php artisan migrate:install
docker-composer exec acs php artisan migrate
docker-composer exec acs php artisan db:seed
docker-composer exec acs supervisorctl restart goacs
