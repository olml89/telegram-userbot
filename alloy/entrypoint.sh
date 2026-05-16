#!/bin/sh
set -eu

CONF="/etc/alloy/config.alloy"

echo "✅ Container up [alloy]."
exec alloy run "$CONF"
