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

# Set bin/bot-manager.php (which starts a React loop) as the entrypoint
ENTRYPOINT ["php", "bin/bot-manager.php"]

FROM base AS prod

# Copy the needed codebases to the container
COPY bot-manager/ ./
COPY application/ /telegram-userbot/application/
COPY bot-runtime/ /telegram-userbot/bot-runtime/

# Copy supervisord config files
COPY bot-manager/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Install PHP dependencies (without require-dev, optimized autoload)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY dev/bin/composer/composer-install /usr/local/bin/composer-install

RUN composer-install bot-runtime bot-manager && \
    rm /usr/local/bin/composer && \
    rm /usr/local/bin/composer-install

# Set the ownership of the container to www-data
RUN chown -R www-data:www-data /telegram-userbot

USER www-data

FROM base AS dev

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

ARG XDEBUG_PORT
COPY docker-php-ext-xdebug.ini /tmp/docker-php-ext-xdebug.ini
RUN sed "s|\${XDEBUG_PORT}|$XDEBUG_PORT|g" /tmp/docker-php-ext-xdebug.ini > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
