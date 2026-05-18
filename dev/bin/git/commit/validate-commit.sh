#!/bin/sh
set -eu

# Sync platform requirements from modules into dev/composer.json
echo "🔍 Syncing platform reqs..."
if ! ./bin/git/commit/sync-platform-reqs.php; then
	echo "❌ sync-platform-reqs.php failed."
	exit 1
fi

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

echo "🔍 Checking code refactoring (rector)..."
if ! composer rector -- --dry-run; then
	echo "❌ rector checks failed. Run rector refactoring before commiting."
	exit 1
fi

exit 0
