#!/bin/sh
set -e

echo "🔧 Loading pre-commit git hook..."
cp bin/git/hooks/pre-commit /telegram-userbot/.git/hooks/pre-commit
chmod +x /telegram-userbot/.git/hooks/pre-commit

echo "🔧 Make phpunit/phpunit.sh, phpstan/phpstan.sh and pint/pint.sh scripts executable..."
chmod +x phpunit/phpunit.sh
chmod +x phpstan/phpstan.sh
chmod +x pint/pint.sh

# Install dependencies
#
# Dependencies from shared, backend, bot and bot-manager are:
# - already installed because of the depends_on directive on the docker-compose.dev.yml
# - properly installed because each Dockerfile installs the needed platform-reqs
/telegram-userbot/shared/bin/composer-install.sh dev

# Maintain the container always up with tail
echo "✅ Container up [tail -f /dev/null]."
tail -f /dev/null
