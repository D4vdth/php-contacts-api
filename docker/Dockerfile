FROM php:8.5-cli AS build

RUN docker-php-ext-install

COPY --from=composer:latest /usr/bin/composer /usr/

WORKDIR /var/www/html/

EXPOSE 8000

CMD "php -s 0.0.0.0:8000 -t public"