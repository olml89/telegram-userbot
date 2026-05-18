FROM redis:8-alpine AS dev

FROM dev AS prod

# Copy redis configuration
COPY redis/redis.conf /usr/local/etc/redis/redis.conf
