#!/bin/sh

SERVICE=""
CI=false

while [ "$1" != "" ]; do
	case $1 in
		--service=*) SERVICE="${1#*=}" ;;
		--ci) CI=true ;;
		*)
			echo "❌ Unknown option: $1"
			exit 1
			;;
	esac
	shift
done

run_phpstan() {
	SERVICE=$1
	NO_PROGRESS_FLAG=""

	echo "🔍 Running phpstan for '$SERVICE'..."

	# Dynamic autoload file
	AUTOLOAD="/telegram-userbot/$SERVICE/vendor/autoload.php"
	echo "🔍 Autoload file: $AUTOLOAD"

	# Dynamic config file
	CONFIG="/telegram-userbot/$SERVICE/phpstan.neon"
	echo "🔍 Configuration file: $CONFIG"

	if $CI; then
		NO_PROGRESS_FLAG="--no-progress"
	fi

	# Enable Opcache
	#
	# The --ansi flag forces colored output, even when the command is run in a non-interactive shell
    # (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
    if ! php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" -d memory_limit=4G ./vendor/bin/phpstan analyse \
    	--ansi \
    	--autoload-file="$AUTOLOAD" \
    	--configuration="$CONFIG" \
	 	$NO_PROGRESS_FLAG
	then
		echo "❌ phpstan found errors in '$SERVICE'"
		exit 1
	fi
}

if [ -z "$SERVICE" ]; then
	# Analyze all services
	run_phpstan "backend"
	run_phpstan "bot-manager"
	run_phpstan "bot"
	run_phpstan "shared"
else
	case "$SERVICE" in
		backend|bot-manager|bot|shared)
			run_phpstan "$SERVICE"
			;;
		*)
		echo "❌ Unknown service: $SERVICE"
		exit 1
		;;
	esac
fi

exit 0
