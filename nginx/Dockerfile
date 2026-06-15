FROM nginx:stable-alpine AS dev

WORKDIR /var/www/html

FROM dev AS prod

# Copy redis configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy built assets after the building stage
COPY --from=telegram-userbot-frontend /app/public/build/assets /var/www/html/assets
