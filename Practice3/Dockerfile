FROM php:8.3-fpm-alpine

RUN apk add --no-cache php83-mysqli php83-pdo_mysql \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable mysqli pdo_mysql

WORKDIR /var/www/html

COPY . /var/www/html

CMD ["php-fpm"]