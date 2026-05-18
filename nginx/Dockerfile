FROM nginx:stable-alpine AS dev

FROM dev AS prod

# Copy redis configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
