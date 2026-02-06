FROM nginx:stable-alpine

# Copy redis configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy entrypoint and change its permissions to be executable
COPY nginx/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
