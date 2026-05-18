#!/bin/sh

SERVICE=""
FILTER=""
COVERAGE=false
CI=false
DEBUG=false

while [ $# -gt 0 ]; do
	case $1 in
		--service=*) SERVICE="${1#*=}" ;;
		--filter=*)
			# Add filters separated by whitespace
			if [ -z "$FILTER" ]; then
				FILTER="${1#*=}"
			else
				FILTER="$FILTER ${1#*=}"
			fi
			;;
		--coverage) COVERAGE=true ;;
		--ci) CI=true ;;
		--debug) DEBUG=true;;
		*)
			echo "❌ Unknown option: $1"
			exit 1
			;;
	esac
	shift
done

run_phpunit() {
	SERVICE=$1

	echo "🔍 Running phpunit for '$SERVICE'..."

	# By default: Opcache enabled (Xdebug disabled)
	PHP="php -n -c /usr/local/etc/php/docker-php-ext-opcache.ini"

	# On --debug, --ci or --coverage: enable Xdebug (Opcache disabled)
	if $DEBUG || $COVERAGE || $CI; then
		PHP="XDEBUG_TRIGGER=1 php -n -c /usr/local/etc/php/docker-php-ext-xdebug.ini"
		echo "🔍 Xdebug enabled"
	else
		echo "🔍 Opcache enabled"
	fi

	# Dynamic configuration file
	CONFIG="/telegram-userbot/$SERVICE/phpunit.xml.dist"
	echo "🔍 Configuration file: $CONFIG"

	PHPUNIT="./vendor/bin/phpunit --colors=always --configuration $CONFIG"

	if [ -n "$FILTER" ]; then
		# phpunit does not support --filter=Class1 --filter=Class2
		# We have to convert it to --filter "Class1|Class2"
		FILTER_REGEX=$(echo "$FILTER" | sed 's/ /|/g')

		echo "🔍 Using --filter '$FILTER_REGEX'"
		PHPUNIT="$PHPUNIT --filter \"$FILTER_REGEX\""
	fi

	# On --coverage: add text coverage
	if $COVERAGE; then
		PHPUNIT="$PHPUNIT --coverage-text"
		echo "🔍 Add text coverage"
	fi

	# On --ci: add clover coverage
	if $CI; then
		PHPUNIT="$PHPUNIT --coverage-clover var/clover.xml"
		echo "🔍 Add clover coverage"
	fi

	if ! eval "$PHP $PHPUNIT"; then
		echo "❌ phpunit found errors in '$SERVICE'"
		exit 1
	fi
}

if [ -z "$SERVICE" ]; then
	# Test all services
	run_phpunit "application"
	run_phpunit "bot-runtime"
	run_phpunit "bot"
	run_phpunit "bot-manager"
	run_phpunit "backend"
else
	case "$SERVICE" in
		application|bot-runtime|bot|bot-manager|backend)
			run_phpunit "$SERVICE"
			;;
		*)
			echo "❌ Unknown service: $SERVICE"
			exit 1
			;;
	esac
fi

exit 0
