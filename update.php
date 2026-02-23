#!/usr/bin/env php
<?php

/**
 * Remix Icon Updater
 *
 * Downloads the latest Remix Icon SVGs from GitHub and regenerates index.js
 * https://github.com/Remix-Design/RemixIcon
 *
 * Usage: php site/plugins/kirby-remixicon/update.php
 */

require_once __DIR__ . '/src/IconUpdater.php';

LaurentTacco\KirbyRemixicon\IconUpdater::update(__DIR__);
