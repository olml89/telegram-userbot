FROM php:8.5-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/dev

# Install additional PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug
RUN install-php-extensions intl pgsql pdo_pgsql # backend
RUN install-php-extensions pcntl posix ffi      # bot
RUN install-php-extensions redis                # bot-manager

# In PHP 8.5 Opcache is already integrated into the core. Copy the Opcache configuration
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

FROM base AS dev

# Install composer and clear extensions installer
RUN install-php-extensions @composer && rm /usr/local/bin/install-php-extensions

# Xdebug dev config (allow coverage mode, start on trigger)
ARG XDEBUG_PORT=9003
COPY dev/docker-php-ext-xdebug.dev.ini /usr/local/etc/php/docker-php-ext-xdebug.ini
RUN printf '\nxdebug.client_port=%s\n' ${XDEBUG_PORT} >> /usr/local/etc/php/docker-php-ext-xdebug.ini

FROM base AS ci

# Install git and composer
# - curl, bash, composer: needed to install dependencies during the build phase, don't keep it in the container
RUN apk add --no-cache git curl bash && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Xdebug ci config (only coverage mode)
COPY dev/docker-php-ext-xdebug.ci.ini /usr/local/etc/php/docker-php-ext-xdebug.ini

# Copy the entire project into the image
COPY . /telegram-userbot/

# Make all CI needed scripts executable
RUN chmod +x /telegram-userbot/shared/bin/composer-install.sh && \
    chmod +x /telegram-userbot/dev/phpstan/phpstan.sh && \
    chmod +x /telegram-userbot/dev/phpunit/phpunit.sh && \
    chmod +x /telegram-userbot/dev/pint/pint.sh && \
    chmod +x /telegram-userbot/dev/rector/rector.sh

# Pre-install PHP dependencies to cache them in the image
RUN /telegram-userbot/shared/bin/composer-install.sh dev shared backend bot bot-manager

# Clear composer, curl, bash and extensions installer
RUN rm /usr/local/bin/composer && \
    apk del curl bash && \
    rm /usr/local/bin/install-php-extensions
