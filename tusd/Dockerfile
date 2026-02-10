FROM tusproject/tusd:latest AS base

USER root

FROM base AS prod

# Copy entrypoint.sh
COPY tusd/entrypoint.sh /entrypoint.sh

FROM base AS dev
