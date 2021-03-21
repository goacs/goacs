#!/usr/bin/env bash

docker-compose up node
docker-compose up -d goacs
docker-compose exec acs composer install --no-dev
docker-compose exec acs php artisan key:generate
docker-compose exec acs php artisan jwt:secret --force
docker-compose exec acs php artisan migrate:install
docker-compose exec acs php artisan migrate
docker-compose exec acs php artisan db:seed
docker-compose exec acs supervisorctl restart goacs
