FROM php:8.5-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/dev

# Install PHP compilation dependencies
# - icu-dev:        needed to install intl PHP extension
# - postgresql-dev: needed to install pgsql and pdo_pgsql PHP extensions
# - libffi-dev:     needed to install ffi PHP extension
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    autoconf \
    make \
    g++ \
    linux-headers \
    icu-dev \
    postgresql-dev \
    libffi-dev

# Install backend PHP extensions (core)
RUN docker-php-ext-install \
    intl \
    pgsql \
    pdo_pgsql

# Install bot PHP extensions (core)
RUN docker-php-ext-install \
    pcntl \
    posix \
    ffi

# Install bot-manager redis extension (PECL)
RUN pecl install redis && docker-php-ext-enable redis

# Install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install needed runtime dependencies
# - git:            on dev: prepare the repository on the pre-commit
#                   on CI: install dependencies on Github when dist fails due to rate limiting HTTP/2 429)
# - nodejs, npm:    run tsc type checks
RUN apk add --no-cache \
    git \
    nodejs \
    npm

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# In PHP 8.5 Opcache is already integrated into the core. Copy the Opcache configuration
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

FROM base AS dev

# Immutable dev environment
ENV APP_ENV=dev

# Configure Git to trust the repository directory (solves dubious ownership issues with mounted volumes)
RUN git config --global --add safe.directory /telegram-userbot

# Xdebug dev config (allow coverage mode, start on trigger)
ARG XDEBUG_PORT
COPY dev/docker/php/docker-php-ext-xdebug.dev.ini /tmp/docker-php-ext-xdebug.ini
RUN sed "s|\${XDEBUG_PORT}|$XDEBUG_PORT|g" /tmp/docker-php-ext-xdebug.ini > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

FROM base AS ci

# Immutable CI environment
ENV APP_ENV=ci

# Xdebug ci config (only coverage mode)
COPY dev/docker/php/docker-php-ext-xdebug.ci.ini /usr/local/etc/php/docker-php-ext-xdebug.ini

# Copy the entire project into the image
COPY . /telegram-userbot/

# Pre-install PHP dependencies to cache them in the image
COPY dev/bin/composer/composer-install /usr/local/bin/composer-install
RUN composer-install application bot-runtime bot bot-manager backend dev

# Pre-install npm dependencies (in the backend context, to run frontend assets quality checks) to cache them in the image
RUN npm ci --prefix /telegram-userbot/backend
