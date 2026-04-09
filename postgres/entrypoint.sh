#!/usr/bin/env sh
set -eu

LOG="/var/log/postgres"
echo "🔧 Creating ${LOG}..."
mkdir -p ${LOG}
chown -R postgres:postgres ${LOG}

ENTRYPOINT="/usr/local/bin/docker-entrypoint.sh"
echo "✅ Container up [postgres]."
exec ${ENTRYPOINT} postgres "$@"
