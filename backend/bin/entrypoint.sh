#!/bin/sh
set -eu

# In environments that are not prod, install dependencies (since they are not baked into the container image)
if [ "$APP_ENV" != "prod" ]; then
    /telegram-userbot/dev/bin/composer/composer-install.sh application backend
fi

echo "✅ Container up [php-fpm]."
php-fpm
