#!/usr/bin/env php
<?php

declare(strict_types=1);

set_error_handler(function (int $severity, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$projectRoot = dirname(__DIR__, 3);
$devComposer = $projectRoot . '/dev/composer.json';
$modules = ['shared', 'backend', 'bot', 'bot-manager'];

echo "🔍 Collecting ext-* requirements from modules: " . implode(', ', $modules) . "\n";

// Collect all unique ext-* keys from all modules
$extensions = [];

foreach ($modules as $module) {
    $moduleComposer = $projectRoot . '/' . $module . '/composer.json';

    if (!file_exists($moduleComposer)) {
        echo "⚠️ Skipping {$module} (no {$moduleComposer} found)\n";
        continue;
    }

    $json = json_decode(file_get_contents($moduleComposer), true);

    foreach ($json['require'] ?? [] as $pkg => $ver) {
        if (str_starts_with($pkg, 'ext-')) {
            $extensions[$pkg] = '*';
        }
    }
}

echo "📦 Collected extensions: " . json_encode($extensions) . "\n";

// Merge into dev/composer.json
$dev = json_decode(file_get_contents($devComposer), true);
$dev['require'] ??= [];
$phpVersion = null;

// Remove old ext-* and php entries, keep anything else in require
foreach (array_keys($dev['require']) as $key) {
    if ($key === 'php') {
        $phpVersion = $dev['require'][$key];
        unset($dev['require'][$key]);

        continue;
    }

    if (str_starts_with($key, 'ext-')) {
        unset($dev['require'][$key]);
    }
}

// Retrieve php requirement
$dev['require']['php'] = $phpVersion ?? throw new RuntimeException('No PHP requirement found in dev/composer.json');

// Sort and add extensions
ksort($extensions);
$dev['require'] = array_merge($dev['require'], $extensions);

// Write back with pretty print
file_put_contents(
    $devComposer,
    json_encode($dev, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n",
);

echo "✅ dev/composer.json updated with platform requirements\n";
