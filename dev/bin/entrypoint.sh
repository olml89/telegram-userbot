#!/bin/sh
set -e

echo "🔧 Loading pre-commit git hook..."
cp bin/git/hooks/pre-commit /telegram-userbot/.git/hooks/pre-commit
chmod +x /telegram-userbot/.git/hooks/pre-commit

echo "🔧 Make phpunit/phpunit.sh, phpstan/phpstan.sh and pint/pint.sh scripts executable..."
chmod +x phpunit/phpunit.sh
chmod +x phpstan/phpstan.sh
chmod +x pint/pint.sh

echo "🔧 Installing dev dependencies..."
/telegram-userbot/shared/bin/composer-install.sh

# Maintain the container always up with tail
echo "✅ Container up [tail -f /dev/null]."
tail -f /dev/null
