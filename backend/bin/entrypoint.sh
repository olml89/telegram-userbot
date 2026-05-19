#!/bin/sh
set -eu

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
    /telegram-userbot/dev/bin/composer/composer-install.sh application backend
fi

echo "✅ Container up [php-fpm]."
php-fpm
