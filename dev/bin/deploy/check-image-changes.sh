#!/bin/sh
set -e

# Checks if any image-affecting files changed between two commits.
#
# Usage:
#   CI:    check-image-changes.sh <event_name> <before_sha> <current_sha> <github_output_file>
#   Local: check-image-changes.sh
#
# When run without arguments, compares HEAD~1..HEAD and prints the result to stdout.

EVENT_NAME="${1:-local}"
BEFORE_SHA="$2"
CURRENT_SHA="$3"
GITHUB_OUTPUT="$4"

PROJECT_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"

IMAGE_PATHS="dev/Dockerfile dev/composer.json docker-php-ext-opcache.ini"

REBUILD=false

# On pull requests, compare against the base branch
if [ "$EVENT_NAME" = "pull_request" ] || [ "$EVENT_NAME" = "local" ]; then
    CHANGED_FILES=$(git -C "$PROJECT_ROOT" diff --name-only HEAD~1 HEAD)
else
    CHANGED_FILES=$(git -C "$PROJECT_ROOT" diff --name-only "$BEFORE_SHA" "$CURRENT_SHA")
fi

for path in $IMAGE_PATHS; do
    if echo "$CHANGED_FILES" | grep -q "^${path}$"; then
        echo "🔄 Changed: $path"
        REBUILD=true
    fi
done

echo "🔧 rebuild=$REBUILD"

if [ -n "$GITHUB_OUTPUT" ]; then
    echo "rebuild=$REBUILD" >> "$GITHUB_OUTPUT"
fi
