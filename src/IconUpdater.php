<?php

namespace LaurentTacco\KirbyRemixicon;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class IconUpdater
{
    private const GITHUB_ZIP = 'https://github.com/Remix-Design/RemixIcon/archive/refs/heads/master.zip';
    private const GITHUB_PKG = 'https://raw.githubusercontent.com/Remix-Design/RemixIcon/master/package.json';
    private const PLUGIN_ID  = 'laurenttacco/kirby-remixicon';

    /**
     * Download and generate Remix Icons.
     *
     * @param string        $targetDir  Plugin directory (where index.js lives)
     * @param callable|null $logger     Optional callback for log messages
     * @return bool True if icons were updated, false if already up to date or on error
     */
    public static function update(string $targetDir, ?callable $logger = null): bool
    {
        $log = $logger ?? function (string $msg): void {
            echo $msg . "\n";
        };

        $context = stream_context_create([
            'http' => [
                'timeout'         => 30,
                'user_agent'      => 'kirby-remixicon-updater',
                'follow_location' => true,
            ],
        ]);

        // Check latest version on GitHub
        $log('Checking latest version...');

        $localVersion = null;
        $versionFile  = $targetDir . '/.remixicon-version';

        if (is_file($versionFile)) {
            $localVersion = trim(file_get_contents($versionFile));
        }

        $remotePkg = @file_get_contents(self::GITHUB_PKG, false, $context);

        if ($remotePkg) {
            $remoteVersion = json_decode($remotePkg, true)['version'] ?? null;

            if ($localVersion && $remoteVersion && $localVersion === $remoteVersion) {
                $log("Already up to date (v{$localVersion}).");
                return false;
            }

            if ($remoteVersion) {
                $log(
                    'New version available: v' . $remoteVersion .
                    ($localVersion ? " (current: v{$localVersion})" : '')
                );
            }
        }

        // Download zip from GitHub
        $log('Downloading icons from GitHub...');

        $zipData = @file_get_contents(self::GITHUB_ZIP, false, $context);

        if (!$zipData) {
            $log('Error: Could not download from GitHub.');
            return false;
        }

        $uid    = uniqid('remix_');
        $tmpZip = sys_get_temp_dir() . "/{$uid}.zip";
        $tmpDir = sys_get_temp_dir() . "/{$uid}";

        file_put_contents($tmpZip, $zipData);

        // Extract
        $log('Extracting...');

        $zip = new ZipArchive();

        if ($zip->open($tmpZip) !== true) {
            $log('Error: Could not open zip archive.');
            @unlink($tmpZip);
            return false;
        }

        @mkdir($tmpDir, 0755, true);
        $zip->extractTo($tmpDir);
        $zip->close();
        @unlink($tmpZip);

        // Find extracted root folder
        $extracted = glob($tmpDir . '/RemixIcon-*');

        if (empty($extracted) || !is_dir($extracted[0])) {
            $log('Error: Unexpected archive structure.');
            self::cleanup($tmpDir);
            return false;
        }

        $root     = $extracted[0];
        $iconsDir = $root . '/icons';

        if (!is_dir($iconsDir)) {
            $log('Error: Icons directory not found.');
            self::cleanup($tmpDir);
            return false;
        }

        // Read version from package.json
        $version = null;
        $pkgPath = $root . '/package.json';

        if (is_file($pkgPath)) {
            $pkg     = json_decode(file_get_contents($pkgPath), true);
            $version = $pkg['version'] ?? null;
        }

        // Collect all SVG files
        $log('Processing icons...');

        $svgs     = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($iconsDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'svg') {
                $svgs[] = $file->getPathname();
            }
        }

        usort($svgs, fn($a, $b) => strcmp(basename($a, '.svg'), basename($b, '.svg')));

        // Build index.js
        $lines   = ["panel.plugin('" . self::PLUGIN_ID . "', {", '  icons: {'];
        $count   = count($svgs);
        $current = 0;

        foreach ($svgs as $svgPath) {
            $current++;
            $name    = basename($svgPath, '.svg');
            $content = file_get_contents($svgPath);

            if (preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $content, $match)) {
                $inner = trim($match[1]);
                $inner = str_replace("'", "\\'", $inner);
                $comma = $current < $count ? ',' : '';
                $lines[] = "    '{$name}': '{$inner}'{$comma}";
            }
        }

        $lines[] = '  }';
        $lines[] = '});';

        file_put_contents($targetDir . '/index.js', implode("\n", $lines) . "\n");

        // Write version file
        if ($version) {
            file_put_contents($versionFile, $version . "\n");
        }

        // Cleanup
        self::cleanup($tmpDir);

        $log("[kirby-remixicon] Built {$count} icons" . ($version ? " (v{$version})" : ''));

        return true;
    }

    private static function cleanup(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }

        @rmdir($dir);
    }
}
