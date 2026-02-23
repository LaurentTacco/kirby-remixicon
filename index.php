<?php

use Kirby\Cms\App as Kirby;

// Auto-download icons on first load
$indexJs = __DIR__ . '/index.js';

if (filesize($indexJs) < 1000) {
    require_once __DIR__ . '/src/IconUpdater.php';
    LaurentTacco\KirbyRemixicon\IconUpdater::update(__DIR__);
}

// Read Remix Icon version
$version     = null;
$versionFile = __DIR__ . '/.remixicon-version';

if (is_file($versionFile)) {
    $version = trim(file_get_contents($versionFile));
}

Kirby::plugin('laurenttacco/kirby-remixicon', [
    'info' => [
        'version'  => $version,
        'license'  => 'MIT',
        'homepage' => 'https://github.com/laurenttacco/kirby-remixicon',
    ],
]);
