FROM nginx:stable-alpine AS base

# Copy redis configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy entrypoint and change its permissions to be executable
COPY nginx/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

FROM telegram-userbot-backend:prod AS assets

FROM base AS prod

# Copy generated assets
RUN mkdir -p /telegram-userbot/backend/public
COPY --from=assets /telegram-userbot/backend/public/build /telegram-userbot/backend/public/build

FROM base AS dev
