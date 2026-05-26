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

FROM base AS assets

WORKDIR /telegram-userbot/backend

# Install npm
RUN apk add --no-cache nodejs npm

# Install npm packages (vite)
COPY backend/package.json backend/package-lock.json ./
RUN npm ci

# Copy the assets and the vite config and run build to generate the assets in public/build
COPY backend/assets ./assets
COPY backend/vite.config.js ./
COPY backend/public ./public
RUN npm run build

FROM base AS prod

# Copy the backend codebase to the container
COPY backend/ ./

# Copy application codebase to the container
COPY application/ /telegram-userbot/application/

# Copy built assets from assets stage directly to public/build, overwriting anything there
COPY --from=assets /telegram-userbot/backend/public/build /telegram-userbot/backend/public/build

# Copy production PHP-FPM configuration
COPY backend/php-fpm/www.prod.conf /usr/local/etc/php-fpm.d/www.conf

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install PHP dependencies (without require-dev, optimized autoload)
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --working-dir=/telegram-userbot/application && \
    composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Clear composer
RUN rm /usr/local/bin/composer

# Pre-create runtime directories to avoid Docker volume mount edge cases where the mount point does not exist and is
# created as root-owned, which would break expected permissions for the application user (www-data UID 82).
# Then set the ownership of the container to www-data. This ensures consistent ownership from first container startup.
RUN mkdir -p var/uploads var/content && \
    chown -R www-data:www-data /telegram-userbot

USER www-data

FROM base AS dev

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy xdebug configuration file
COPY docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Override xdebug default port with the environment provided one
ARG XDEBUG_PORT=9003
RUN printf '\nxdebug.client_port=%s\n' ${XDEBUG_PORT} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
