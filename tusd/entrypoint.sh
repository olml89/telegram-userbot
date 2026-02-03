#!/bin/sh
set -e

LOG="/telegram-userbot/shared/var/log/tusd"
echo "🔧 Creating ${LOG}..."
mkdir -p ${LOG}

UPLOADS="/telegram-userbot/shared/var/uploads"
echo "🔧 Creating ${UPLOADS}..."
mkdir -p ${UPLOADS}

echo "✅ Container up [tusd]."
exec tusd \
  -base-path "${TUSD_BASE_PATH}" \
  -upload-dir "${TUSD_UPLOAD_DIR}" \
  -behind-proxy \
  > "${UPLOADS}/tusd.log" 2>&1
