FROM php:7.1-apache

RUN apt-get update && \
    apt-get install -y libxml2-dev && \
    docker-php-ext-install soap

COPY . /var/www/html
# No newline at end of file
