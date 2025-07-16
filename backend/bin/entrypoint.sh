#!/bin/sh
set -e

echo "🔧 Installing backend dependencies..."
/telegram-userbot/shared/bin/composer-install.sh

echo "✅ Container up [php-fpm]."
php-fpm
