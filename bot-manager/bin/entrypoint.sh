#!/bin/sh
set -eu

# In environments that are not prod, install dependencies (since they are not baked into the container image)
if [ "$APP_ENV" != "prod" ]; then
	/telegram-userbot/dev/bin/composer/composer-install.sh bot-runtime bot-manager
fi

# Start bot-manager.php, which starts a React loop
echo "✅ Container up [bin/bot-manager.php]."
php bin/bot-manager.php
