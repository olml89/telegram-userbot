FROM redis:8-alpine

# Copy redis configuration
COPY redis/redis.conf /usr/local/etc/redis/redis.conf

# Copy entrypoint and change its permissions to be executable
COPY redis/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
