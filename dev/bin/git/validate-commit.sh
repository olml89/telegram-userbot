#!/bin/sh
set -e

# Sync platform requirements from modules into dev/composer.json
/telegram-userbot/dev/bin/sync-platform-reqs.sh

echo "🔍 Validating commit..."

echo "🔍 Unit tests (phpunit)..."
if ! composer phpunit; then
  echo "❌ phpunit failed. Fix failing tests before commiting."
  exit 1
fi

echo "🔍 Code static analysis (phpstan)..."
if ! composer phpstan; then
  echo "❌ phpstan failed. Fix code before commiting."
  exit 1
fi

echo "🔍 Checking code linting (pint)..."
if ! composer pint -- --test; then
  echo "❌ pint checks failed. Run pint linting before commiting."
  exit 1
fi

echo "✅ All checks have passed. Proceeding with the commit."
exit 0
