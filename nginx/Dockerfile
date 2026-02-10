FROM nginx:stable-alpine AS base

FROM base AS prod

# Copy redis configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy entrypoint.sh
COPY nginx/entrypoint.sh /entrypoint.sh

FROM base AS dev
