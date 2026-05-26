#!/bin/sh
set -eu

# It checks if staged files have CRLF line endings
#
# Usage:
#   check-staged-files-line-endings [-f]
#
# Options:
#   --f     Automatically convert CRLF line endings to LF and git add the modified files

PROJECT_ROOT=$(cd "$(dirname "$0")/../../../../.." && pwd)
FORCE_UPDATE=false;
CRLF_FILES=false

while [ $# -gt 0 ]; do
    case $1 in
        -f)
            FORCE_UPDATE=true
            ;;
        *)
            echo "❌ Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done

# Prevent grep from failing with exit code 1 when no files match the filter (required due to set -e)
STAGED_FILES=$(git -C "$PROJECT_ROOT" diff --cached --name-only | grep -v '^\.git/' || true)

for STAGED_FILE in $STAGED_FILES; do
    FILE_PATH="$PROJECT_ROOT/$STAGED_FILE"

    if [ -f "$FILE_PATH" ] && grep -q "$(printf '\r')" "$FILE_PATH"; then
        if ! $FORCE_UPDATE; then
            echo "❌ File with CRLF line endings: $STAGED_FILE"
            CRLF_FILES=true

            continue
        fi

        set -- sed -i 's/\r$//' "$FILE_PATH"
        printf '🔄 %s\n' "$*"
        "$@"

        set -- git \
            -C "$PROJECT_ROOT" \
            add "$STAGED_FILE"

        printf '📦 %s\n' "$*"
        "$@"
    fi
done

if $CRLF_FILES; then
    exit 1
fi

echo "🔍 [check-staged-files-line-endings.sh] all staged files are normalized"





