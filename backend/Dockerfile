FROM php:8.5-fpm-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/backend

# Install PHP compilation dependencies
# - icu-dev:        needed to install intl PHP extension
# - postgresql-dev: needed to install pgsql and pdo_pgsql PHP extensions
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    autoconf \
    make \
    g++ \
    linux-headers \
    icu-dev \
    postgresql-dev

# Install PHP extensions (core)
RUN docker-php-ext-install \
    intl \
    pgsql \
    pdo_pgsql

# Install needed runtime dependencies
# - ffmpeg:         get resolution and duration and strip metadata from audio and video files
# - exiftool:       strip metadata from image and pdf files
# - ghostcript:     strip metadata from pdf files
# - poppler-utils:  installs pdfsig to determine if a pdf is signed ot not
# - qpdf:           determines if a pdf has AcroForms, JS or Attachments
RUN apk add --no-cache \
    ffmpeg \
    ghostscript \
    exiftool \
    poppler-utils \
    qpdf

# In PHP 8.5 Opcache is already integrated into the core. Copy the Opcache configuration
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

FROM node:26-alpine AS assets

WORKDIR /telegram-userbot/backend

# Install npm packages (vite)
COPY backend/package.json backend/package-lock.json ./
RUN npm ci

# Copy the assets and the vite config
COPY backend/assets ./assets
COPY backend/public ./public
COPY backend/vite.config.js ./

# Generate the assets in public/build
RUN npm run build

FROM base AS prod

# Immutable prod environment
ENV APP_ENV=prod

# Copy the needed codebases to the container
COPY backend/ ./
COPY application/ /telegram-userbot/application/

# Copy built assets from assets stage directly to public/build, overwriting anything there
COPY --from=assets /telegram-userbot/backend/public/build /telegram-userbot/backend/public/build

# Copy production PHP-FPM configuration
COPY backend/docker/php-fpm/www.prod.conf /usr/local/etc/php-fpm.d/www.conf

# Install PHP dependencies (without require-dev, optimized autoload)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY dev/bin/composer/composer-install /usr/local/bin/composer-install

RUN composer-install application backend && \
    rm /usr/local/bin/composer && \
    rm /usr/local/bin/composer-install

# Set the ownership of the container to www-data
RUN chown -R www-data:www-data /telegram-userbot

# Add www-data to the shared upload-managers group for the telegram-userbot-uploads volume
ARG UPLOAD_MANAGERS_GID
RUN addgroup -S -g ${UPLOAD_MANAGERS_GID} upload-managers &&  \
    addgroup www-data upload-managers

USER www-data

# Pre-create internal runtime var/contents directory to avoid root-owned mount point for telegram-userbot-uploads
RUN mkdir -p var/contents

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
