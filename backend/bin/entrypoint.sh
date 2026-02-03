#!/bin/sh
set -e

CONSOLE="/telegram-userbot/backend/bin/console"
echo "🔧 Making ${CONSOLE} executable..."
chmod +x "${CONSOLE}"

CACHE="/telegram-userbot/backend/var/cache"
echo "🔧 Creating ${CACHE}..."
mkdir -p "${CACHE}"
chown -R www-data:www-data "${CACHE}"

LOG="/telegram-userbot/shared/var/log/backend"
echo "🔧 Creating ${LOG}..."
mkdir -p ${LOG}
chown -R www-data:www-data ${LOG}

# It might have been created by tusd, we try to create it and make it readable/movable by www-data
UPLOADS="/telegram-userbot/shared/var/uploads"
echo "🔧 Creating ${UPLOADS}..."
mkdir -p ${UPLOADS}
chown -R www-data:www-data ${UPLOADS}

CONTENT="/telegram-userbot/shared/var/content"
echo "🔧 Creating ${CONTENT}..."
mkdir -p ${CONTENT}
chown -R www-data:www-data ${CONTENT}

# In production environment, copy built assets from the intermediate directory to the public build
if [ "$APP_ENV" = "prod" ]; then
	COMPILED="/telegram-userbot/backend/var/build"
	BUILD="/telegram-userbot/backend/public/build"
	echo "🔧 Copying compiled assets from ${COMPILED} to ${BUILD}..."

	if [ -d "${COMPILED}" ]; then
		# Remove contents of BUILD, not BUILD itself
		rm -rf "${BUILD:?}/"*
		mkdir -p "${BUILD}"

		# Copy the contents of COMPILED, not COMPILED itself
		cp -R "${COMPILED}/." "${BUILD}"
		rm -rf "${COMPILED}"
	fi
fi

# Install backend dependencies
/telegram-userbot/shared/bin/composer-install.sh backend

echo "✅ Container up [php-fpm]."
php-fpm
