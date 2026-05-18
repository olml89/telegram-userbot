FROM grafana/loki:latest AS dev

# loki is a distroless image without shell, so we cannot enter the container on runtime

FROM dev AS prod

# Copy loki configuration
COPY loki/local-config.yaml /etc/loki/local-config.yaml
