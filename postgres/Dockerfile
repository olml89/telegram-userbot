FROM postgres:17-alpine

# Copy postgres configuration
COPY postgres/postgresql.conf /etc/postgresql/postgresql.conf

# Copy entrypoint and change its permissions to be executable
COPY postgres/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
