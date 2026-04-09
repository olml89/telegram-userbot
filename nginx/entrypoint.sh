#!/bin/sh
set -e

LOG="/var/log/nginx"
echo "🔧 Creating ${LOG}..."
mkdir -p ${LOG}

echo "✅ Container up [nginx]."
exec nginx -g 'daemon off;'
