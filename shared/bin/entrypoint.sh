#!/bin/sh
set -e

echo "🔧 Installing shared dependencies..."
./bin/composer-install.sh

# Maintain the container always up with tail
echo "✅ Container up [tail -f /dev/null]."
tail -f /dev/null
