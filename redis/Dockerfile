FROM redis:8-alpine AS base

FROM base AS prod

# Copy redis configuration
COPY redis/redis.conf /usr/local/etc/redis/redis.conf

# Copy entrypoint.sh
COPY redis/entrypoint.sh /usr/local/bin/entrypoint.sh

FROM base AS dev
