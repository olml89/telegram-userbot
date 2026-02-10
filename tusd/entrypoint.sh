#!/bin/sh
set -e

LOG="/var/log/tusd"
echo "🔧 Creating ${LOG}..."
mkdir -p ${LOG}

echo "🔧 Creating ${TUSD_UPLOAD_DIR}..."
mkdir -p TUSD_UPLOAD_DIR

echo "✅ Container up [tusd]."
exec tusd \
  -base-path "${TUSD_BASE_PATH}" \
  -upload-dir "${TUSD_UPLOAD_DIR}" \
  -behind-proxy \
  > "${LOG}/tusd.log" 2>&1
