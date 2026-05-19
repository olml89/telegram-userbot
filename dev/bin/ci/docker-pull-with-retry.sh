#!/bin/sh
set -eu

IMAGE="$1"

for i in 1 2 3 4 5; do
    echo "🔧 Pull attempt $i for $IMAGE"

    if docker pull "$IMAGE"; then
        echo "✅ Pull successful"
        exit 0
    fi

    echo "🟡 Pull failed, retrying in 15s..."
    sleep 15
done

echo "❌ Failed to pull image after retries"
exit 1
