#!/bin/sh
set -eu

UPLOAD_DIR="/srv/tusd-data/data"
LOCK_FILE="$UPLOAD_DIR/.initialized"

if [ -f "$LOCK_FILE" ]; then
    echo "$LOCK_FILE already exists"
    exit 0
fi

# Apply ACL recursively to existing files and directories:
# - Grants read/write/execute (rwX) to the upload-managers group
setfacl -R -m g:"$UPLOAD_MANAGERS_GID":rwX "$UPLOAD_DIR"

# Set default ACL for the directory:
# - Ensures that all newly created files and directories inherit read/write/execute (rwX) to the upload-managers group
setfacl -d -m g:"$UPLOAD_MANAGERS_GID":rwX "$UPLOAD_DIR"

touch "$LOCK_FILE" && echo "Created: $LOCK_FILE"
getfacl /srv/tusd-data/data | grep "$UPLOAD_MANAGERS_GID" || exit 1
