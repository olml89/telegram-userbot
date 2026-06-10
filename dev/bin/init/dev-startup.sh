#!/bin/sh
set -eu

if [ "${APP_ENV}" != "dev" ]; then
    echo "❌ only allowed in development (APP_ENV=${APP_ENV})";
    exit 1;
fi

UID=$(id -u)
GID=$(id -g)

SERVICES="
application
bot-runtime
bot
bot-manager
backend
dev
vite
"

# Mount tusd:/srv/tusd-data/data with UID:GID
# Mount backend:/telegram-userbot/backend/var/contents and backend:/telegram-userbot/backend/var/uploads with UID:GID
RUNTIMES="
.runtime
backend/var
"

create_cache() {
    echo "🔧 Creating cache directories..."

    for SERVICE in $SERVICES; do
        if [ ! -e "${SERVICE:?}/var" ] && mkdir -p "${SERVICE:?}/var"; then
            echo "Created: ${SERVICE:?}/var ($UID:$GID)"
        fi
    done
}

create_runtime() {
    echo "🔧 Creating runtime directories..."

    for RUNTIME in $RUNTIMES; do
        if [ ! -e "${RUNTIME:?}/contents" ] && mkdir -p "${RUNTIME:?}/contents"; then
            echo "Created: ${RUNTIME:?}/contents ($UID:$GID)"
        fi
        if [ ! -e "${RUNTIME:?}/uploads" ] && mkdir -p "${RUNTIME:?}/uploads"; then
            echo "Created: ${RUNTIME:?}/uploads ($UID:$GID)"
        fi
    done
}

create_cache
create_runtime
