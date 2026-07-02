FROM php:8.5-cli AS build

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin

WORKDIR /var/www/html/

COPY ./docker/entrypoint.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT [ "entrypoint.sh" ]

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]