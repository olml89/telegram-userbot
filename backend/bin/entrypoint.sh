#!/bin/sh
set -e

echo "🔧 Creating /telegram-userbot/backend/var/log..."
mkdir -p /telegram-userbot/backend/var/log

echo "🔧 Creating /telegram-userbot/backend/var/cache..."
mkdir -p /telegram-userbot/backend/var/cache

# Making var directory writable to www-data (user of the php-fpm workers)
chown -R www-data:www-data /telegram-userbot/backend/var

# Install dependencies
/telegram-userbot/shared/bin/composer-install.sh shared backend

echo "✅ Container up [php-fpm]."
php-fpm
