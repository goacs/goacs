#!/bin/bash

set -m -e -x
set -eo pipefail
shopt -s nullglob

declare -g GOACS_INITIALIZED

check_is_initialized() {
  if [ -f "/.goacs-initialized" ]; then
    GOACS_INITIALIZED='true'
  fi
}

run_laravel_commands() {
  key_exists=$(php artisan key:generate --show)

  if [ -z "$key_exists" ]; then
      php artisan key:generate
  fi

  jwt_exists=$(php artisan jwt:secret --show)

  if [ -z "jwt_exists" ]; then
        php artisan jwt:secret
  fi

  php artisan migrate:install -q || true
  php artisan migrate --force
  php artisan db:seed --force
}


_main() {
  if [ -z "$DATABASE_ALREADY_EXISTS" ]; then
    check_is_initialized "$@"
    run_laravel_commands "$@"
  fi

}

_main "$@"
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf --nodaemon

