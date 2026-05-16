#!/bin/sh

SERVICE=""
DRY_RUN=false

while [ $# -gt 0 ]; do
	case $1 in
		--service=*) SERVICE="${1#*=}" ;;
		--dry-run) DRY_RUN=true ;;
		*)
			echo "❌ Unknown option: $1"
			exit 1
			;;
	esac
	shift
done

run_rector() {
	SERVICE=$1
	DRY_RUN_FLAG=""

	if $DRY_RUN; then
		DRY_RUN_FLAG="--dry-run"
	fi

	echo "🔍 Running rector${DRY_RUN_FLAG:+ $DRY_RUN_FLAG} for '$SERVICE'"

	# Dynamic config file
	CONFIG="/telegram-userbot/$SERVICE/rector.php"
	echo "🔍 Configuration file: $CONFIG"

	# Enable Opcache
	#
	# The --ansi flag forces colored output, even when the command is run in a non-interactive shell
    # (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
	if ! php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" ./vendor/bin/rector \
		--ansi \
		--config="$CONFIG" \
		$DRY_RUN_FLAG
	then
		echo "❌ rector found errors in '$SERVICE'"
		exit 1
	fi
}

if [ -z "$SERVICE" ]; then
	# Format all services
	run_rector "application"
	run_rector "bot-runtime"
	run_rector "bot"
	run_rector "bot-manager"
	run_rector "backend"
else
	case "$SERVICE" in
		application|bot-runtime|bot|bot-manager|backend)
			run_rector "$SERVICE"
			;;
		*)
			echo "❌ Unknown service: $SERVICE"
			exit 1
			;;
	esac
fi

exit 0
