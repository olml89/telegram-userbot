#!/bin/sh
set -eu

echo "✅ Container up [tusd]."

# Let it print out logs in stdout
exec tusd \
  -base-path "${TUSD_UPLOAD_ENDPOINT}" \
  -upload-dir "${TUSD_UPLOAD_DIR}" \
  -hooks-http http://nginx"${API_VALIDATION_ENDPOINT}" \
  -hooks-enabled-events pre-create \
  -behind-proxy
