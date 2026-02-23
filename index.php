<?php

use Kirby\Cms\App as Kirby;

// Auto-download icons on first load
$indexJs = __DIR__ . '/index.js';

if (filesize($indexJs) < 1000) {
    require_once __DIR__ . '/src/IconUpdater.php';
    LaurentTacco\KirbyRemixicon\IconUpdater::update(__DIR__);
}

// Read Remix Icon version for update checks
$remixVersion = null;
$versionFile  = __DIR__ . '/.remixicon-version';

if (is_file($versionFile)) {
    $remixVersion = trim(file_get_contents($versionFile));
}

Kirby::plugin('laurenttacco/kirby-remixicon', [
    'options' => [
        'remixicon' => $remixVersion,
    ],
]);
