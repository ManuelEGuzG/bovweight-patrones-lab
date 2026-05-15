<?php
/**
 * Autoloader PSR-4 manual para ejecutar las demos sin necesidad de composer install.
 * Si tienes composer instalado, puedes usar vendor/autoload.php en su lugar.
 */
spl_autoload_register(function ($class) {
    $prefixes = [
        'BovWeight\\Lab\\Tests\\' => __DIR__ . '/tests/',
        'BovWeight\\Lab\\'        => __DIR__ . '/src/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
