#!/bin/sh
set -e

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
else
	# In environments that are not prod, install dependencies (since they are not baked into the container image)
    /telegram-userbot/shared/bin/composer-install.sh shared backend
fi

CACHE="/telegram-userbot/backend/var/cache"
echo "🔧 Creating ${CACHE}..."
mkdir -p "${CACHE}"
chown -R www-data:www-data "${CACHE}"

LOG="/var/log/backend"
echo "🔧 Creating ${LOG}..."
mkdir -p ${LOG}
chown -R www-data:www-data ${LOG}

UPLOADS="/var/uploads"
echo "🔧 Creating ${UPLOADS}..."
mkdir -p ${UPLOADS}
chown -R www-data:www-data ${UPLOADS}

CONTENT="/var/content"
echo "🔧 Creating ${CONTENT}..."
mkdir -p ${CONTENT}
chown -R www-data:www-data ${CONTENT}

echo "✅ Container up [php-fpm]."
php-fpm
