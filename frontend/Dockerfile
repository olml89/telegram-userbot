FROM node:26-alpine AS dev

WORKDIR /telegram-userbot/backend

FROM dev AS build

WORKDIR /app

# Install npm packages (vite)
COPY backend/package.json backend/package-lock.json ./
RUN npm ci

# Copy the assets and the vite config
COPY backend/assets ./assets
COPY backend/public ./public
COPY backend/vite.config.ts ./

# Generate the assets in public/build
RUN npm run build
