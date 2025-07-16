#!/bin/sh
set -e

echo "🔧 Installing bot-manager dependencies..."
/telegram-userbot/shared/bin/composer-install.sh

# Start bot-manager.php, which starts a React loop
echo "✅ Container up [bin/bot-manager.php]."
php bin/bot-manager.php
