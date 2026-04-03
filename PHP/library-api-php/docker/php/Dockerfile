FROM php:8.5-fpm-alpine

# Install essential dev tools
RUN apk add --no-cache git unzip bash

# Inject Composer directly into the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html