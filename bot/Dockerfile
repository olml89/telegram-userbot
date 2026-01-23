FROM alpine:3.22 AS build

########################################################################################################################
# This step compiles PrimeModule-ext library: https://prime.madelineproto.xyz/                                         #
# It is used by MadelineProto to factorize huge prime numbers very fast.                                               #
########################################################################################################################

# Set the temporary image working directory
WORKDIR /tmp

# Install system tools so MadelineProto can compile PrimeModule-ext
# - git: needed to clone the repository
# - build-base: needed to compile C/C++
# - cmake: needed by PrimeModule to build
# - netcat-openbsd: needed to perform the health check for the container
RUN apk add --no-cache \
    git \
    build-base \
    cmake \
    netcat-openbsd

# Clone and compile PrimeModule-ext to libprimemodule.so
RUN git clone https://github.com/danog/PrimeModule-ext && \
    cd PrimeModule-ext && \
    make -j$(nproc)

FROM php:8.4-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/bot

# Install needed runtime dependencies
# - libstdc++: C++ runtime, needed to execute '.so' compiled with g++
# - supervisor: supervisord to manage /telegram-userbot/bot/bin/action/runner.php
RUN apk add --no-cache  \
    libstdc++  \
    supervisor

# Copy the compiled libprimemodule.so and the primemodule-ffi.h into the container to use the library through FFI
COPY --from=build /tmp/PrimeModule-ext/libprimemodule.so /usr/lib/
COPY --from=build /tmp/PrimeModule-ext/src/primemodule-ffi.h /usr/include/

# Copy entrypoint.sh during the build phase, before the docker volume is mounted, and make it executable
COPY bot/bin/entrypoint.sh bin/entrypoint.sh
RUN chmod +x bin/entrypoint.sh

# Copy supervisord config files during the build phase, before the docker volume is mounted
COPY bot/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY bot/supervisor/conf.d/*.conf /etc/supervisor/conf.d/

# Install additional PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pcntl posix ffi

FROM base AS prod

# Copy the bot codebase to the container
COPY bot/ ./

# Copy the shared codebase to the container
COPY shared/ /telegram-userbot/shared/

# Enable Opcache
RUN docker-php-ext-enable opcache
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

# Install composer
# - curl, bash, composer: needed to install dependencies during the build phase, don't keep it in the container
RUN apk add --no-cache curl bash && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies (without require-dev, optimized autoload)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Clear composer, curl, bash and extensions installer
RUN rm /usr/local/bin/composer && \
    apk del curl bash && \
    rm /usr/local/bin/install-php-extensions

FROM base AS dev

# Install composer and xdebug and clear extensions installer
RUN install-php-extensions xdebug @composer && rm /usr/local/bin/install-php-extensions;

# Copy xdebug configuration file
COPY docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Override xdebug default port with the environment provided one
ARG XDEBUG_PORT=9003
RUN printf '\nxdebug.client_port=%s\n' ${XDEBUG_PORT} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;
