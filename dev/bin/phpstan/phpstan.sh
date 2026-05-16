#!/bin/sh
set -eu

SERVICE=""
NO_PROGRESS_FLAG=""

while [ $# -gt 0 ]; do
	case $1 in
		--service=*) SERVICE="${1#*=}" ;;
		--ci) NO_PROGRESS_FLAG="--no-progress" ;;
		*)
			echo "❌ Unknown option: $1"
			exit 1
			;;
	esac
	shift
done

run_integration_analysis() {
		echo "🔍 Running phpstan..."

    	# Integration config file
    	CONFIG="/telegram-userbot/dev/bin/phpstan/phpstan.integration.neon"
    	echo "🔍 Configuration file: $CONFIG"

		# Enable Opcache
		#
		# The --ansi flag forces colored output, even when the command is run in a non-interactive shell
		# (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
		if ! php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" -d memory_limit=4G ./vendor/bin/phpstan analyse \
			--ansi \
			--configuration="$CONFIG" \
			$NO_PROGRESS_FLAG
		then
			echo "❌ phpstan found errors"
			exit 1
		fi
}

run_service_analysis() {
	SERVICE=$1

	echo "🔍 Running phpstan for '$SERVICE'..."

	# Dynamic config file
	CONFIG="/telegram-userbot/$SERVICE/phpstan.neon"
	echo "🔍 Configuration file: $CONFIG"

	# Enable Opcache
	#
	# The --ansi flag forces colored output, even when the command is run in a non-interactive shell
    # (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
    if ! php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" -d memory_limit=4G ./vendor/bin/phpstan analyse \
    	--ansi \
    	--configuration="$CONFIG" \
	 	$NO_PROGRESS_FLAG
	then
		echo "❌ phpstan found errors in '$SERVICE'"
		exit 1
	fi
}

if [ -z "$SERVICE" ]; then
	run_integration_analysis
else
	case "$SERVICE" in
		application|bot-runtime|bot|bot-manager|backend)
			run_service_analysis "$SERVICE"
			;;
		*)
		echo "❌ Unknown service: $SERVICE"
		exit 1
		;;
	esac
fi

exit 0
