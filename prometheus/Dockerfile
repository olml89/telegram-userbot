FROM prom/prometheus:latest AS dev

FROM dev AS prod

# Copy prometheus configuration
COPY prometheus/prometheus.yml /etc/prometheus/prometheus.yml
