#!/bin/sh
set -eu

# Checks if any dev image file changed between two commits.
#
# Usage:
#   CI:    check-dev-image-changes.sh <event_name> <before_sha> <current_sha> <github_output_file>
#   Local: check-dev-image-changes.sh
#
# When run without arguments, compares HEAD~1..HEAD and prints the result to stdout.

EVENT_NAME="${1:-local}"
BEFORE_SHA="${2:-}"
AFTER_SHA="${3:-}"
GITHUB_OUTPUT="${4:-}"

PROJECT_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
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

for file in $CHANGED_FILES; do
    case "$file" in
        dev/*)
            echo "🔄 Changed in dev image: $file"
            REBUILD=true
            ;;
    esac
done

$REBUILD && REBUILD_STATUS="✅" || REBUILD_STATUS="⚪"
echo "$REBUILD_STATUS rebuild=$REBUILD"

if [ -n "$GITHUB_OUTPUT" ]; then
    echo "$REBUILD_STATUS rebuild=$REBUILD" >> "$GITHUB_OUTPUT"
fi
