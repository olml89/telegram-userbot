#!/bin/sh
set -e

echo "🔧 Making /telegram-userbot/backend/bin/console executable..."
chmod +x /telegram-userbot/backend/bin/console

echo "🔧 Creating /telegram-userbot/backend/var/cache..."
mkdir -p /telegram-userbot/backend/var/cache

# Making var directory writable to www-data (user of the php-fpm workers)
chown -R www-data:www-data /telegram-userbot/backend/var

# Install backend dependencies
/telegram-userbot/shared/bin/composer-install.sh backend

echo "✅ Container up [php-fpm]."
php-fpm
