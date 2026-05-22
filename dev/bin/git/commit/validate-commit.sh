#!/bin/sh
set -eu

# Sync platform requirements from modules into dev/composer.json
echo "🔍 Syncing platform reqs..."
if ! ./bin/git/commit/sync-platform-reqs.php; then
	echo "❌ sync-platform-reqs.php failed."
	exit 1
fi

echo "🔍 Checking PHP tests (phpunit)..."
if ! ./bin/phpunit/phpunit.sh; then
	echo "❌ phpunit failed. Fix failing tests before commiting."
	exit 1
fi

echo "🔍 Checking PHP code static analysis (phpstan)..."
if ! ./bin/phpstan/phpstan.sh; then
	echo "❌ phpstan failed. Fix code before commiting."
	exit 1
fi

echo "🔍 Checking PHP code linting (pint)..."
if ! ./bin/pint/pint.sh; then
	echo "❌ pint checks failed. Run pint linting before commiting."
	exit 1
fi

echo "🔍 Checking PHP code refactoring (rector)..."
if ! ./bin/rector/rector.sh; then
	echo "❌ rector checks failed. Run rector refactoring before commiting."
	exit 1
fi

echo "🔍 Checking TypeScript code (tsc)..."
if ! ./bin/tsc/tsc.sh; then
	echo "❌ tsc checks failed. Fix code before commiting."
	exit 1
fi

exit 0
