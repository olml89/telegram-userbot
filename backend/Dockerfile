FROM php:8.5-fpm-alpine AS base

# Set the container working directory
WORKDIR /telegram-userbot/backend

# Install needed runtime dependencies
# - ffmpeg: get resolution and duration and strip metadata from audio and video files
# - imagick: strip metadata from image files
RUN apk add --no-cache \
    ffmpeg \
    imagemagick

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions intl pgsql pdo_pgsql imagick

FROM base AS assets

WORKDIR /telegram-userbot/backend

# Install npm
RUN apk add --no-cache nodejs npm

# Install npm packages (vite)
COPY backend/package.json backend/package-lock.json ./
RUN npm ci --include=dev

# Copy the assets and the vite config and run build to generate the assets in public/build
COPY backend/assets ./assets
COPY backend/vite.config.js ./
COPY backend/public ./public
RUN npm run build

FROM base AS prod

# Copy the backend codebase to the container
COPY backend/ ./

# Copy shared codebase to the container
COPY shared/ /telegram-userbot/shared/

# Copy built assets from assets stage to a compiled intermediate directory
COPY --from=assets /telegram-userbot/backend/public/build /telegram-userbot/backend/var/build

# Create writable cache dir for Symfony
RUN mkdir -p /telegram-userbot/backend/var/cache  &&  \
    chown -R www-data:www-data /telegram-userbot/backend/var

# Make bin/console executable
RUN chmod +x /telegram-userbot/backend/bin/console

# In PHP 8.5 Opcache is already integrated into the core. Copy the Opcache configuration
COPY docker-php-ext-opcache.ini /usr/local/etc/php/docker-php-ext-opcache.ini

# Install composer
# - curl, bash, composer: needed to install dependencies during the build phase, don't keep them in the container
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

# Add Node.js + npm for Vite (to bundle assets on dev environment)
RUN apk add --no-cache nodejs npm

# Copy xdebug configuration file
COPY docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Override xdebug default port with the environment provided one
ARG XDEBUG_PORT=9003
RUN printf '\nxdebug.client_port=%s\n' ${XDEBUG_PORT} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;
