<?php

/**
 * Primary runtime autoloader for the plugin classes plus the bundled Laposta API v2 library.
 *
 * WordPress boots the plugin through laposta-signup-basic.php, which includes this file.
 * In Composer-managed installs, the root project autoloader may also know this namespace.
 */

spl_autoload_register(function ($class) {
    // Configuration for different namespaces and their base directories
    $namespaces = [
        'Laposta\\SignupBasic\\' => __DIR__ . '/src/',
    ];

    // Check each namespace configuration
    foreach ($namespaces as $prefix => $baseDir) {
        // If the class starts with this namespace prefix
        if (strpos($class, $prefix) === 0) {
            // Get the relative class path by removing the namespace prefix
            $relativeClass = substr($class, strlen($prefix));

            // Build the full file path by converting namespace separators to directory separators
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            // Load the file if it exists
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

/**
 * Register the version-scoped Laposta API v2 autoloader.
 */
if (PHP_VERSION_ID >= 80000) {
    spl_autoload_register(function ($class) {
        $prefixes = [
            'LapostaApi230\\Vendor\\Psr\\Http\\Client\\' => [
                __DIR__ . '/includes/laposta-api-php-2/vendor/psr/http-client/src/',
            ],
            'LapostaApi230\\Vendor\\Psr\\Http\\Message\\' => [
                __DIR__ . '/includes/laposta-api-php-2/vendor/psr/http-message/src/',
                __DIR__ . '/includes/laposta-api-php-2/vendor/psr/http-factory/src/',
            ],
            'LapostaApi230\\' => [
                __DIR__ . '/includes/laposta-api-php-2/src/',
            ],
        ];

        foreach ($prefixes as $prefix => $baseDirs) {
            if (strpos($class, $prefix) !== 0) {
                continue;
            }

            $relativeClass = substr($class, strlen($prefix));
            $relativePath = str_replace('\\', '/', $relativeClass) . '.php';

            foreach ($baseDirs as $baseDir) {
                $file = $baseDir . $relativePath;
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    });
}
