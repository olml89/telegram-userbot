#!/bin/sh
set -e

echo "🔧 Creating /telegram-userbot/bot/var directory for the supervisord.pid file..."
mkdir -p /telegram-userbot/bot/var

echo "🔧 Creating /telegram-userbot/shared/var/log/bot directory for the supervisord.log in the shared container..."
mkdir -p /telegram-userbot/shared/var/log/bot

echo "🔧 Installing bot dependencies..."
/telegram-userbot/shared/bin/composer-install.sh

# Use exec to replace the shell and start with PID 1
echo "✅ Container up [supervisord]."
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
