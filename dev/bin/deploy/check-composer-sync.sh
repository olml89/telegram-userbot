#!/bin/sh
set -e

FAILED=0

check_composer() {
  DIR="/telegram-userbot/$1"

  if [ -f "$DIR/composer.json" ]; then
    echo "🔍 Checking $DIR..."

    if [ ! -f "$DIR/composer.lock" ]; then
      echo "❌ $DIR missing composer.lock"
      FAILED=1
      return
    fi

    if ! composer install \
      --dry-run \
      --no-interaction \
      --working-dir="$DIR" > /dev/null 2>&1; then

      echo "❌ $DIR/composer.json is not in sync with $DIR/composer.lock"
      FAILED=1
    else
      echo "✅ $DIR/composer.json is in sync with $DIR/composer.lock"
    fi
  fi
}

check_composer "backend"
check_composer "bot"
check_composer "bot-manager"
check_composer "dev"

exit $FAILED
