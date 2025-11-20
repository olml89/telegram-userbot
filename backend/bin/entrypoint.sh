#!/bin/sh
set -e

# Install dependencies
/telegram-userbot/shared/bin/composer-install.sh shared backend

echo "✅ Container up [php-fpm]."
php-fpm
