#!/bin/sh
set -e

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

echo "🔍 Applying code linting (pint)..."
if ! composer pint; then
  echo "❌ pint failed. Exiting."
  exit 1
fi

echo "✅ All checks have passed. Proceeding with the commit."
exit 0
