FROM alpine:3.22 AS build

########################################################################################################################
# This step compiles PrimeModule-ext library: https://prime.madelineproto.xyz/                                         #
# It is used by MadelineProto to factorize huge prime numbers very fast.                                               #
########################################################################################################################

# Set the temporary image working directory
WORKDIR /tmp

# Install system tools so MadelineProto can compile PrimeModule-ext
# - git:            needed to clone the repository
# - build-base:     needed to compile C/C++
# - cmake:          needed by PrimeModule to build
RUN apk add --no-cache \
    git \
    build-base \
    cmake

# Clone and compile PrimeModule-ext to libprimemodule.so
RUN git clone https://github.com/danog/PrimeModule-ext && \
    cd PrimeModule-ext && \
    make -j$(nproc)

FROM php:8.5-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/bot

# Install PHP compilation dependencies
# - libffi-dev: needed to install ffi PHP extension
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    autoconf \
    make \
    g++ \
    linux-headers \
    libffi-dev

# Install PHP extensions (core)
RUN docker-php-ext-install \
    pcntl \
    posix \
    ffi

# Install needed runtime dependencies
# - libstdc++:  C++ runtime, needed to execute '.so' compiled with g++
# - supervisor: supervisord to manage /telegram-userbot/bot/bin/action/runner.php
RUN apk add --no-cache \
    libstdc++ \
    supervisor

# Copy the compiled libprimemodule.so and the primemodule-ffi.h into the container to use the library through FFI
COPY --from=build /tmp/PrimeModule-ext/libprimemodule.so /usr/lib/
COPY --from=build /tmp/PrimeModule-ext/src/primemodule-ffi.h /usr/include/

# In PHP 8.5 Opcache is already integrated into the core. Copy the Opcache configuration
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

# Set supervisord as the entrypoint
ENTRYPOINT ["/usr/bin/supervisord"]

FROM base AS prod

# Immutable prod environment
ENV APP_ENV=prod

# Copy the needed codebases to the container
COPY bot/ ./
COPY application/ /telegram-userbot/application/
COPY bot-runtime/ /telegram-userbot/bot-runtime/

# Copy supervisord config files
COPY bot/docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY bot/docker/supervisor/conf.d/*.conf /etc/supervisor/conf.d/

# Install PHP dependencies (without require-dev, optimized autoload)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY dev/bin/composer/composer-install /usr/local/bin/composer-install

RUN composer-install bot-runtime bot && \
    rm /usr/local/bin/composer && \
    rm /usr/local/bin/composer-install

# Set the ownership of the container to www-data
RUN chown -R www-data:www-data /telegram-userbot

USER www-data

# Pre-create internal runtime var directory to allocate supervisord.pid
RUN mkdir -p var

FROM base AS dev

# Immutable dev environment
ENV APP_ENV=dev

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

ARG XDEBUG_PORT
COPY docker-php-ext-xdebug.ini /tmp/docker-php-ext-xdebug.ini
RUN sed "s|\${XDEBUG_PORT}|$XDEBUG_PORT|g" /tmp/docker-php-ext-xdebug.ini > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
