#!/bin/sh
set -eu

# Checks if any dev image affecting files changed between two commits.
#
# Usage:
#   CI:    check-image-changes.sh <event_name> <before_sha> <current_sha> <github_output_file>
#   Local: check-image-changes.sh
#
# When run without arguments, compares HEAD~1..HEAD and prints the result to stdout.

EVENT_NAME="${1:-local}"
BEFORE_SHA="${2:-}"
AFTER_SHA="${3:-}"
GITHUB_OUTPUT="${4:-}"

PROJECT_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
IMAGE_PATHS="dev/Dockerfile dev/composer.json dev/docker-php-ext-opcache.ini"
REBUILD=false

if [ "$EVENT_NAME" = "local" ]; then
    # Local: staged + unstaged changes
    DIFF_COMMANDS=$(
        {
            git -C "$PROJECT_ROOT" diff --cached --name-only
            git -C "$PROJECT_ROOT" diff --name-only
        }
    )
else
    # CI: analyze all commits in the push (BEFORE_SHA..AFTER_SHA)
    DIFF_COMMANDS=$(git -C "$PROJECT_ROOT" diff --name-only "$BEFORE_SHA..$AFTER_SHA")
fi

CHANGED_FILES=$(echo "$DIFF_COMMANDS" | sort -u)

echo "Changed files:"
echo "$CHANGED_FILES"

for path in $IMAGE_PATHS; do
    if echo "$CHANGED_FILES" | grep -q "^${path}$"; then
        echo "🔄 Changed: $path"
        REBUILD=true
    fi
done

if [ "$REBUILD" = "true" ]; then
    REBUILD_STATUS="✅"
else
    REBUILD_STATUS="⚪"
fi

echo "$REBUILD_STATUS rebuild=$REBUILD"

if [ -n "$GITHUB_OUTPUT" ]; then
    echo "$REBUILD_STATUS rebuild=$REBUILD" >> "$GITHUB_OUTPUT"
fi
