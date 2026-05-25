FROM php:8.5-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/dev

# Install git
#
# in dev: needed to prepare the repository on the pre-commit
# in CI: needed to install dependencies on Github when dist fails due to rate limiting HTTP/2 429
RUN apk add --no-cache git

# Install additional PHP extensions and clear extensions installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug @composer
RUN install-php-extensions intl pgsql pdo_pgsql # backend
RUN install-php-extensions pcntl posix ffi      # bot
RUN install-php-extensions redis                # bot-manager
RUN rm /usr/local/bin/install-php-extensions

# In PHP 8.5 Opcache is already integrated into the core. Copy the Opcache configuration
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

# Install npm
RUN apk add --no-cache nodejs npm

FROM base AS dev

# Configure Git to trust the repository directory (solves dubious ownership issues with mounted volumes)
RUN git config --global --add safe.directory /telegram-userbot

# Xdebug dev config (allow coverage mode, start on trigger)
ARG XDEBUG_PORT=9003
COPY dev/docker-php-ext-xdebug.dev.ini /usr/local/etc/php/docker-php-ext-xdebug.ini
RUN printf '\nxdebug.client_port=%s\n' ${XDEBUG_PORT} >> /usr/local/etc/php/docker-php-ext-xdebug.ini

FROM base AS ci

# Set CI environment
ENV APP_ENV=ci

# Xdebug ci config (only coverage mode)
COPY dev/docker-php-ext-xdebug.ci.ini /usr/local/etc/php/docker-php-ext-xdebug.ini

# Copy the entire project into the image
COPY . /telegram-userbot/

# Pre-install PHP dependencies to cache them in the image
RUN ./bin/composer/composer-install.sh application bot-runtime bot bot-manager backend dev

# Pre-install npm dependencies (in the backend context, to run frontend assets quality checks) to cache them in the image
RUN npm ci --prefix /telegram-userbot/backend
