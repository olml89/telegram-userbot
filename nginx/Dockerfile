FROM nginx:stable-alpine AS dev

FROM dev AS prod

# Copy redis configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy built assets after the building stage
COPY --from=telegram-userbot-frontend /app/public/build/assets /telegram-userbot/backend/public/build/assets
