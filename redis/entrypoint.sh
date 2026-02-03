#!/usr/bin/env sh
set -eu

LOG="/telegram-userbot/shared/var/log/redis"
echo "🔧 Creating ${LOG}..."
mkdir -p ${LOG}
chown -R redis:redis ${LOG}

CONF="/usr/local/etc/redis/redis.conf"
echo "✅ Container up [redis-server]."
exec su -s /bin/sh -c "redis-server ${CONF}" redis
