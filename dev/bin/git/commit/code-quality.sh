#!/bin/sh
set -eu

# Runs all the code quality checks. It is equivalent to running:
#   dev/bin/phpstan/phpstan.sh
#   dev/bin/phpstan/pint.sh --test [SERVICES...]
#   dev/bin/phpstan/rector.sh --dry-run [SERVICES...]
#   dev/bin/phpstan/phpunit.sh [SERVICES...]
#   dev/bin/phpstan/tsc.sh --noEmit [SERVICES...]
#
# Usage:
#   code-quality.sh [SERVICES...]
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)

run_php_checks() {
    ./bin/phpstan/phpstan.sh "$@"
    ./bin/pint/pint.sh --test "$@"
    ./bin/rector/rector.sh --dry-run "$@"
    ./bin/phpunit/phpunit.sh "$@"
}

run_npm_checks() {
    ./bin/tsc/tsc.sh --noEmit "$@"
}

# Run global checks
if [ $# -eq 0 ]; then
    run_php_checks
    run_npm_checks
fi

# Run service-based checks
while [ $# -gt 0 ]; do
    case $1 in
        application|bot-runtime|bot|bot-manager)
            run_php_checks "$1"
            ;;
        backend)
            run_php_checks "$1"
            run_npm_checks "$1"
            ;;
        *)
            echo "❌ Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done
