FROM php:8.2-fpm-alpine as base

WORKDIR /var/www/php-dev

RUN apk add --no-cache \
    autoconf \
    build-base \
    coreutils \
    g++ \
    gcc \
    git \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    make \
    openssh \
    openssl-dev \
    python3 \
    zip

COPY --from=composer/composer /usr/bin/composer /usr/local/bin/composer
COPY . /var/www/php-dev/

CMD ["php-fpm"]
