#!/bin/sh

SERVICE=""
TEST=false

while [ "$1" != "" ]; do
	case $1 in
		--service=*) SERVICE="${1#*=}" ;;
		--test) TEST=true ;;
		*)
			echo "❌ Unknown option: $1"
			exit 1
			;;
	esac
	shift
done

run_pint() {
	SERVICE=$1
	TEST_FLAG=""

	echo "🔍 Running pint for '$SERVICE' $TEST_FLAG"

	# Dynamic codebase path
	CODE_PATH="/telegram-userbot/$SERVICE"

	# Dynamic config file
	CONFIG="$CODE_PATH/pint.json"
	echo "🔍 Configuration file: $CONFIG"

	if $TEST; then
		TEST_FLAG="--test"
	fi

	# Enable Opcache
	#
	# The --ansi flag forces colored output, even when the command is run in a non-interactive shell
    # (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
	if ! php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" ./vendor/bin/pint \
		--ansi \
		--config="$CONFIG" \
		"$CODE_PATH" \
		$TEST_FLAG
	then
		echo "❌ pint found errors in '$SERVICE'"
		exit 1
	fi
}

if [ -z "$SERVICE" ]; then
	# Format all services
	run_pint "shared"
	run_pint "backend"
	run_pint "bot"
	run_pint "bot-manager"
else
	case "$SERVICE" in
		backend|bot-manager|bot|shared)
			run_pint "$SERVICE"
			;;
		*)
			echo "❌ Unknown service: $SERVICE"
			exit 1
			;;
	esac
fi

exit 0
