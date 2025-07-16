FROM php:8.4-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/dev

# Install additional PHP extensions and clean extensions installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug @composer && rm /usr/local/bin/install-php-extensions;

# Enable Opcache
RUN docker-php-ext-enable opcache
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

FROM base AS dev

# Xdebug dev config (allow coverage mode, start on trigger)
ARG XDEBUG_PORT=9003
COPY dev/docker-php-ext-xdebug.dev.ini /usr/local/etc/php/docker-php-ext-xdebug.ini
RUN printf '\nxdebug.client_port=%s\n' ${XDEBUG_PORT} >> /usr/local/etc/php/docker-php-ext-xdebug.ini;

FROM base AS ci

# Xdebug ci config (onky coverage mode)
COPY dev/docker-php-ext-xdebug.ci.ini /usr/local/etc/php/docker-php-ext-xdebug.ini
