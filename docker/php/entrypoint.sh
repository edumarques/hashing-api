#!/bin/sh

# Install composer packages
composer dump-autoload -o --classmap-authoritative
composer install --no-progress --ignore-platform-reqs
composer dump-autoload -o --classmap-authoritative
composer clear-cache

# Create database
bin/console doctrine:database:create --if-not-exists

# Wait until DB is up and ready for connections
until bin/console dbal:run-sql "select 1" >/dev/null 2>&1; do
  (echo >&2 "Waiting for MySQL to be ready...")
  sleep 1
done

# Run migrations
bin/console doctrine:migrations:migrate --no-interaction

# Run FPM
exec php-fpm