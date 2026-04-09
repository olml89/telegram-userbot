#!/bin/sh
set -e

# Collects all ext-* requirements from module composer.json files
# and syncs them into dev/composer.json

PROJECT_ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
DEV_COMPOSER="$PROJECT_ROOT/dev/composer.json"
MODULES="shared backend bot bot-manager"

echo "🔍 Collecting ext-* requirements from modules: $MODULES"

# Collect all unique ext-* keys from all modules using a portable approach
EXT_JSON="{"
FIRST=true

for MODULE in $MODULES; do
    MODULE_COMPOSER="$PROJECT_ROOT/$MODULE/composer.json"

    if [ ! -f "$MODULE_COMPOSER" ]; then
        echo "⚠️  Skipping $MODULE (no composer.json found)"
        continue
    fi

    # Extract ext-* entries from require section
    EXTS=$(php -r "
        \$json = json_decode(file_get_contents('$MODULE_COMPOSER'), true);
        foreach (\$json['require'] ?? [] as \$pkg => \$ver) {
            if (str_starts_with(\$pkg, 'ext-')) {
                echo \$pkg . \"\n\";
            }
        }
    ")

    for EXT in $EXTS; do
        if [ "$FIRST" = true ]; then
            FIRST=false
        else
            EXT_JSON="$EXT_JSON,"
        fi
        EXT_JSON="$EXT_JSON\"$EXT\":\"*\""
    done
done

EXT_JSON="$EXT_JSON}"

echo "📦 Collected extensions: $EXT_JSON"

# Merge into dev/composer.json using PHP (available in the container)
php -r "
    \$dev = json_decode(file_get_contents('$DEV_COMPOSER'), true);
    \$extensions = json_decode('$EXT_JSON', true);

    // Also keep the php version requirement from the most restrictive module
    \$dev['require'] = \$dev['require'] ?? [];

    // Remove old ext-* and php entries, keep anything else in require
    foreach (array_keys(\$dev['require']) as \$key) {
        if (str_starts_with(\$key, 'ext-') || \$key === 'php') {
            unset(\$dev['require'][\$key]);
        }
    }

    // Add php requirement
    \$dev['require']['php'] = '^8.5';

    // Sort and add extensions
    ksort(\$extensions);
    \$dev['require'] = array_merge(\$dev['require'], \$extensions);

    // Write back with pretty print
    file_put_contents(
        '$DEV_COMPOSER',
        json_encode(\$dev, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . \"\n\"
    );
"

echo "✅ dev/composer.json updated with platform requirements"
