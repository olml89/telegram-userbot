#!/bin/sh
set -e

echo "🔧 Making /telegram-userbot/backend/bin/console executable..."
chmod +x /telegram-userbot/backend/bin/console

echo "🔧 Creating /telegram-userbot/backend/var/cache..."
mkdir -p /telegram-userbot/backend/var/cache
chown -R www-data:www-data /telegram-userbot/backend/var/cache

echo "🔧 Creating /telegram-userbot/shared/var/log/backend..."
mkdir -p /telegram-userbot/shared/var/log/backend
chown -R www-data:www-data /telegram-userbot/shared/var/log/backend

# It might have been created by tusd, we try to create it and make it readable/movable by www-data
echo "🔧 Creating /telegram-userbot/shared/var/uploads..."
mkdir -p /telegram-userbot/shared/var/uploads
chown -R www-data:www-data /telegram-userbot/shared/var/uploads

echo "🔧 Creating /telegram-userbot/shared/var/content..."
mkdir -p /telegram-userbot/shared/var/content
chown -R www-data:www-data /telegram-userbot/shared/var/content

# Install backend dependencies
/telegram-userbot/shared/bin/composer-install.sh backend

echo "✅ Container up [php-fpm]."
php-fpm
