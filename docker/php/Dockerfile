FROM php:8-fpm-alpine

RUN apk update
RUN docker-php-ext-install pdo pdo_mysql mysqli opcache
COPY conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini