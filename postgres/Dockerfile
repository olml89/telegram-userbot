FROM postgres:17-alpine AS base

FROM base AS prod

# Copy postgres configuration
COPY postgres/postgresql.conf /etc/postgresql/postgresql.conf

# Copy entrypoint.sh
COPY postgres/entrypoint.sh /usr/local/bin/entrypoint.sh

FROM base AS dev
