FROM php:8.4-fpm-alpine AS base

# Install composer, pgsql, pdo_pgsql
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions @composer pgsql pdo_pgsql;

# Set app working directory
WORKDIR /telegram-userbot

FROM base AS prod

# Clear extensions installer
RUN rm /usr/local/bin/install-php-extensions;

FROM base AS dev

# Install xdebug and clear extensions installer
RUN install-php-extensions xdebug; \
    rm /usr/local/bin/install-php-extensions;

# Copy xdebug configuration file
COPY ./php-fpm/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Override xdebug default port with the environment provided one
ARG XDEBUG_PORT=9003
RUN printf '\nxdebug.client_port=%s\n' ${XDEBUG_PORT} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;
