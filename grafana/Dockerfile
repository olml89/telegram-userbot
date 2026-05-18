FROM grafana/grafana:latest AS dev

FROM dev AS prod

# Copy grafana configuration
COPY grafana/provisioning /etc/grafana/provisioning
