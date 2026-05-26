FROM php:8.5-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/bot-manager

# Install PHP compilation dependencies
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    autoconf \
    make \
    g++ \
    linux-headers

# Install redis (PECL)
RUN pecl install redis && docker-php-ext-enable redis

# Install needed runtime dependencies
# - supervisor: supervisord to manage bot container
RUN apk add --no-cache \
    supervisor

# In PHP 8.5 Opcache is already integrated into the core. Copy the Opcache configuration
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

FROM base AS prod

# Copy the bot-manager codebase to the container
COPY bot-manager/ ./

# Copy application and bot-runtime codebases to the container
COPY application/ /telegram-userbot/application/
COPY bot-runtime/ /telegram-userbot/bot-runtime/

# Copy supervisord config files
COPY bot-manager/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install PHP dependencies (without require-dev, optimized autoload)
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --working-dir=/telegram-userbot/bot-runtime && \
    composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Clear composer
RUN rm /usr/local/bin/composer

# Set the ownership of the container to www-data
RUN chown -R www-data:www-data /telegram-userbot

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
