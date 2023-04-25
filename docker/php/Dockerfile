ARG PHP_VERSION=8.2

FROM php:${PHP_VERSION}-fpm AS app_php

ARG WORKDIR=/app

RUN apt-get update && apt-get install -y sudo zlib1g-dev g++ git libicu-dev zip libzip-dev libpq-dev libxml2-dev \
    && docker-php-ext-install intl opcache pdo pdo_pgsql xml 

RUN pecl install apcu \
    && pecl install xdebug \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip 

# Post run
RUN  pecl clear-cache \
    && docker-php-source delete \
    && rm -rf /tmp/pear

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/php.ini
COPY docker/php/php-cli.ini $PHP_INI_DIR/conf.d/php-cli.ini
COPY docker/php/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

RUN  apt-get install -y bash

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
RUN apt install symfony-cli

RUN mkdir -p ${WORKDIR}
WORKDIR ${WORKDIR}

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-scripts  --no-progress; \
    composer clear-cache

RUN set -eux \
    && mkdir -p var/cache var/log \
    && chmod -R a+w var \
    && composer dump-autoload --classmap-authoritative

VOLUME ${WORKDIR}/var

