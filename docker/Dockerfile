FROM php:8.5-cli AS build

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin

WORKDIR /var/www/html/

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]