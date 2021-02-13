FROM php:8.0-fpm
WORKDIR "/app"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update -y && \
    apt-get install git -y

# Install selected extensions
RUN apt-get update && \
    apt-get install -y
    apt-get install -y libpq-dev       \
            inotify-tools              \
            libcurl4-openssl-dev       \
            libssl-dev                 \
            supervisor                 \
            unzip                      \
            zlib1g-dev                 \
            --no-install-recommends

RUN pecl install swoole redis

RUN docker-php-ext-enable swoole

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /var/log/supervisor && \
        rm -rf /var/lib/apt/lists/* $HOME/.composer/*-old.phar /usr/bin/qemu-*-static

