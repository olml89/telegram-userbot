#!/bin/sh
set -eu

ENTRYPOINT="/usr/local/bin/docker-entrypoint.sh"
echo "✅ Container up [postgres]."
exec ${ENTRYPOINT} postgres "$@"
