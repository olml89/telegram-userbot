FROM grafana/grafana:latest AS dev

FROM dev AS prod

# Copy grafana configuration
COPY grafana/prod /etc/grafana/provisioning/datasources
