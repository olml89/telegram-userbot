FROM nginx:stable-alpine AS dev

FROM node:26-alpine AS frontend-build

WORKDIR /app

# Install npm packages (vite)
COPY backend/package.json backend/package-lock.json ./
RUN npm ci

# Copy the assets and the vite config
COPY backend/assets ./assets
COPY backend/public ./public
COPY backend/vite.config.js ./

# Generate the compiled assets
RUN npm run build

FROM dev AS prod

# Copy redis configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy built assets after the building stage
COPY --from=frontend-build /app/public/build/assets /telegram-userbot/backend/public/build/assets
