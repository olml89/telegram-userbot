#!/bin/sh
set -e

echo "🔧 Creating /telegram-userbot/shared/var/log/tusd..."
mkdir -p /telegram-userbot/shared/var/log/tusd

echo "🔧 Creating /telegram-userbot/shared/var/uploads..."
mkdir -p /telegram-userbot/shared/var/uploads

echo "✅ Container up [tusd]."
exec tusd \
  -base-path "${TUSD_BASE_PATH}" \
  -upload-dir "${TUSD_UPLOAD_DIR}" \
  -behind-proxy \
  > /telegram-userbot/shared/var/log/tusd/tusd.log 2>&1
