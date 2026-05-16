#!/bin/sh
set -eu

CONF="/usr/local/etc/redis/redis.conf"
echo "✅ Container up [redis-server]."
exec su -s /bin/sh -c "redis-server ${CONF}" redis
