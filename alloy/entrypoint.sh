#!/bin/sh
set -e

LOG="/var/log/alloy"
echo "🔧 Creating ${LOG}..."
mkdir -p ${LOG}

CONF="/etc/alloy/config.alloy"
echo "✅ Container up [alloy]."
exec alloy run "$CONF" > "${LOG}/alloy.log" 2>&1
