FROM grafana/alloy:latest AS base

FROM base AS prod

# Copy alloy configuration
COPY alloy/config.alloy /etc/alloy/config.alloy

# Copy entrypoint.sh
COPY alloy/entrypoint.sh /entrypoint.sh

FROM base AS dev
