FROM php:8.1-cli-alpine

RUN apk update \
    && apk add --no-cache curl g++ make autoconf bash \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && docker-php-ext-install mysqli \
    && pecl install redis \
    && docker-php-ext-enable redis;

COPY ./php-cli/configurations/* /usr/local/etc/php/conf.d/