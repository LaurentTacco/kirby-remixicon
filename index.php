<?php

use Kirby\Cms\App as Kirby;

// Auto-download icons on first load
$indexJs = __DIR__ . '/index.js';

if (filesize($indexJs) < 1000) {
    require_once __DIR__ . '/src/IconUpdater.php';
    LaurentTacco\KirbyRemixicon\IconUpdater::update(__DIR__);
}

// Read plugin version from composer.json
$composer = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);

// Read Remix Icon version
$remixVersion = null;
$versionFile  = __DIR__ . '/.remixicon-version';

if (is_file($versionFile)) {
    $remixVersion = trim(file_get_contents($versionFile));
}

Kirby::plugin('laurenttacco/kirby-remixicon', [
    'info' => [
        'version'  => $composer['version'] ?? '1.0.0',
        'license'  => 'MIT',
        'homepage' => 'https://github.com/LaurentTacco/kirby-remixicon',
        'remixicon' => $remixVersion,
    ],
]);
