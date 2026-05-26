#!/bin/sh
set -eu

# Checks if any dev image file changed between two commits.
#
# Usage:
#   check-dev-image-changes EVENT=local [BEFORE_SHA] [AFTER_SHA] [GITHUB_OUTPUT]

EVENT_NAME="${1:-local}"
BEFORE_SHA="${2:-}"
AFTER_SHA="${3:-}"
GITHUB_OUTPUT="${4:-}"

PROJECT_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
REBUILD=false

ensure_commit() {
    SHA="$1"

    if [ -n "$SHA" ] && ! git -C "$PROJECT_ROOT" cat-file -e "$SHA^{commit}" 2>/dev/null; then
        echo "Fetching missing commit: $SHA"
        git -C "$PROJECT_ROOT" fetch --no-tags origin "$SHA"
    fi
}

if [ "$EVENT_NAME" = "local" ]; then
    # Local: staged changes only
    DIFF_COMMANDS=$(git -C "$PROJECT_ROOT" --name-only --cached)
else
    # CI: compare BEFORE_SHA (parent of first commit in push) against AFTER_SHA (last commit)
    ensure_commit "$BEFORE_SHA"
    ensure_commit "$AFTER_SHA"

    DIFF_COMMANDS=$(git -C "$PROJECT_ROOT" diff --name-only "$BEFORE_SHA" "$AFTER_SHA" )
fi

CHANGED_FILES=$(echo "$DIFF_COMMANDS" | sort -u)

echo "Changed files:"
for file in $CHANGED_FILES; do
    case "$file" in
        dev/*)
            echo "🔄 $file"
            REBUILD=true
            ;;
        *)
            echo "$file"
            ;;
    esac
done

$REBUILD && REBUILD_STATUS="🟢" || REBUILD_STATUS="⚪"
echo "$REBUILD_STATUS rebuild=$REBUILD"

if [ -n "$GITHUB_OUTPUT" ]; then
    echo "rebuild=$REBUILD" >> "$GITHUB_OUTPUT"
fi
