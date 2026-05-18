#!/bin/sh
set -eu

echo "🔧 Loading pre-commit git hook..."
cp bin/git/hooks/pre-commit /telegram-userbot/.git/hooks/pre-commit

# Install dev dependencies
/telegram-userbot/dev/bin/composer/composer-install.sh bot-runtime bot bot-manager backend dev

# Maintain the container always up with tail
echo "✅ Container up [dev]."
tail -f /dev/null
