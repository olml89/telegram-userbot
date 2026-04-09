#!/bin/sh
set -e

echo "🔧 Loading pre-commit git hook..."
cp bin/git/hooks/pre-commit /telegram-userbot/.git/hooks/pre-commit

# Install dev dependencies
/telegram-userbot/shared/bin/composer-install.sh dev shared backend bot bot-manager

# Maintain the container always up with tail
echo "✅ Container up [tail -f /dev/null]."
tail -f /dev/null
