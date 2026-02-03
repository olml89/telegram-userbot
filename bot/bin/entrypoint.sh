#!/bin/sh
set -e

VAR="/telegram-userbot/bot/var"
echo "🔧 Creating ${VAR} directory for the supervisord.pid file..."
mkdir -p ${VAR}

LOG="/telegram-userbot/shared/var/log/bot"
echo "🔧 Creating ${LOG} directory for the supervisord.log in the shared container..."
mkdir -p ${LOG}

# Install shared dependencies (as this is the first microservice: bot-manager depends on bot, and backend on bot-manager)
/telegram-userbot/shared/bin/composer-install.sh shared

# Install bot dependencies
/telegram-userbot/shared/bin/composer-install.sh bot

# Use exec to replace the shell and start with PID 1
CONF="/etc/supervisor/supervisord.conf"
echo "✅ Container up [supervisord]."
exec /usr/bin/supervisord -c ${CONF}
