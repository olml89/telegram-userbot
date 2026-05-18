FROM postgres:17-alpine AS dev

FROM dev AS prod

# Copy postgres configuration
COPY postgres/postgresql.conf /etc/postgresql/postgresql.conf
