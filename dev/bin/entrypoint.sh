#!/bin/sh
set -eu

echo "🔧 Loading pre-commit git hook..."
cp bin/git/hooks/pre-commit /telegram-userbot/.git/hooks/pre-commit

# Install dev dependencies
/telegram-userbot/dev/bin/composer/composer-install.sh dev bot-runtime bot bot-manager backend

# Maintain the container always up with tail
echo "✅ Container up [tail -f /dev/null]."
tail -f /dev/null
