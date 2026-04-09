#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Validates that the PHP extensions installed in dev/Dockerfile match those
 * declared in dev/composer.json. Exits with code 1 on mismatch.
 *
 * Usage: php validate-image-consistency.php
 */

$projectRoot = dirname(__DIR__, 3);
$dockerfile = $projectRoot . '/dev/Dockerfile';
$composerJson = $projectRoot . '/dev/composer.json';

echo "🔍 Checking that dev/Dockerfile extensions match dev/composer.json...\n";

// Extract ext-* from dev/composer.json
$json = json_decode(file_get_contents($composerJson), true);
$composerExts = [];

foreach ($json['require'] ?? [] as $pkg => $ver) {
    if (str_starts_with($pkg, 'ext-')) {
        $composerExts[] = substr($pkg, 4);
    }
}

sort($composerExts);

// Extract extensions from RUN install-php-extensions lines in dev/Dockerfile
$lines = file($dockerfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$ignore = ['xdebug', '@composer'];
$dockerfileExts = [];

foreach ($lines as $line) {
    $line = trim($line);

    // Only match lines that are RUN install-php-extensions commands
    if (!preg_match('/^RUN\s+install-php-extensions\s+(.+)/', $line, $m)) {
        continue;
    }

    // Strip inline comments
    $args = preg_replace('/#.*/', '', $m[1]);

    foreach (preg_split('/\s+/', trim($args)) as $ext) {
        if ($ext !== '' && !in_array($ext, $ignore, true)) {
            $dockerfileExts[] = $ext;
        }
    }
}

$dockerfileExts = array_unique($dockerfileExts);
sort($dockerfileExts);

echo 'dev/composer.json extensions: ' . implode(', ', $composerExts) . "\n";
echo 'dev/Dockerfile extensions: ' . implode(', ', $dockerfileExts) . "\n";

$inComposerOnly = array_diff($composerExts, $dockerfileExts);
$inDockerfileOnly = array_diff($dockerfileExts, $composerExts);

if ($inComposerOnly || $inDockerfileOnly) {
    echo "\n❌ Mismatch between dev/Dockerfile and dev/composer.json!\n";

    if ($inComposerOnly) {
        echo "\nIn dev/composer.json but not in dev/Dockerfile:\n";

        foreach ($inComposerOnly as $e) {
            echo "  - $e\n";
        }
    }

    if ($inDockerfileOnly) {
        echo "\nIn dev/Dockerfile but not in dev/composer.json:\n";

        foreach ($inDockerfileOnly as $e) {
            echo "  - $e\n";
        }
    }

    exit(1);
}

echo "\n✅ dev/Dockerfile and dev/composer.json extensions are in sync.\n";
