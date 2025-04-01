FROM php:8.3-fpm-alpine

RUN apk add --no-cache php83-mysqlnd php83-pdo_mysql \
    php83-phar php83-openssl php83-json php83-mbstring php83-tokenizer \
    git curl unzip bash

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

WORKDIR /var/www/html

COPY ./guestbook/composer.json ./

RUN composer update --no-dev --no-scripts --no-autoloader

COPY ./guestbook /var/www/html

RUN composer dump-autoload --optimize

CMD ["php-fpm"]
