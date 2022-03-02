FROM php:8.0-fpm
WORKDIR "/app"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update -y && \
    apt-get install git -y

# Install selected extensions
RUN apt-get update && \
    apt-get install -y libpq-dev       \
            inotify-tools              \
            libcurl4-openssl-dev       \
            libssl-dev                 \
            supervisor                 \
            unzip                      \
            zlib1g-dev                 \
            nodejs                     \
            npm                        \
            httpie                     \
            --no-install-recommends

RUN pecl install swoole redis

RUN npm install chokidar

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pcntl \
    pdo_mysql

RUN docker-php-ext-enable swoole
RUN docker-php-ext-enable redis
RUN docker-php-ext-enable pcntl

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ADD https://github.com/moparisthebest/static-curl/releases/download/v7.77.0/curl-amd64 /usr/local/bin/curl
RUN chmod +x /usr/local/bin/curl

RUN mkdir -p /var/log/supervisor && \
        rm -rf /var/lib/apt/lists/* $HOME/.composer/*-old.phar /usr/bin/qemu-*-static

CMD ["/usr/bin/supervisord","-c","/etc/supervisor/supervisord.conf"]
