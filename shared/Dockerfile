FROM php:8.4-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/shared

# Install composer and clear extensions installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions @composer && rm /usr/local/bin/install-php-extensions;
