#!/bin/sh
set -eu

# In environments that are not prod, install dependencies (since they are not baked into the container image)
if [ "$APP_ENV" != "prod" ]; then
    /telegram-userbot/dev/bin/composer/composer-install.sh bot-runtime bot
fi

# Use exec to replace the shell and start with PID 1
# Redirect stderr to /dev/null to avoid duplicated logs,
# as the supervisord daemon writes both on /dev/stdout and /dev/null by default
CONF="/etc/supervisor/supervisord.conf"
exec /usr/bin/supervisord -c ${CONF} 2>/dev/null

echo "✅ Container up [supervisord]."
