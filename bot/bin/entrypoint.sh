#!/bin/sh
set -e

# In environments that are not prod, install dependencies (since they are not baked into the container image)
if [ "$APP_ENV" != "prod" ]; then
    /telegram-userbot/shared/bin/composer-install.sh shared bot
fi

VAR="/telegram-userbot/bot/var"
echo "🔧 Creating ${VAR} directory for the supervisord.pid file..."
mkdir -p ${VAR}

LOG="/var/log/bot"
echo "🔧 Creating ${LOG} directory for the supervisord.log in the shared container..."
mkdir -p ${LOG}

# Use exec to replace the shell and start with PID 1
CONF="/etc/supervisor/supervisord.conf"
echo "✅ Container up [supervisord]."
exec /usr/bin/supervisord -c ${CONF}
