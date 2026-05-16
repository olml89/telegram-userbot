#!/bin/sh
set -eu

echo "✅ Container up [nginx]."
exec nginx -g 'daemon off;'
