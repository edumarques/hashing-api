ARG PHP_VERSION=8.0.3

FROM php:${PHP_VERSION}-fpm

RUN apt update && apt install -y \
    acl \
    apt-transport-https \
    build-essential \
    ca-certificates \
    chromium \
    coreutils \
    curl \
    file \
    gettext \
    git \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libmariadb-dev \
    libpng-dev \
    libpq-dev \
    libssl-dev \
    libtool \
    libwebp-dev \
    libxpm-dev \
    libzip-dev \
    mariadb-client \
    wget

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN pecl install xdebug
RUN docker-php-ext-enable opcache xdebug
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype
RUN docker-php-ext-configure zip
RUN docker-php-ext-install -j$(nproc) gd exif intl pdo pdo_mysql zip