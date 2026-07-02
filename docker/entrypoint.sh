#!/bin/sh

set -e

if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

echo "Starting API..."

exec "$@"