<?php
ini_set('session.save_path', __DIR__ . '/tmp');
if (!is_dir(__DIR__ . '/tmp')) {
    mkdir(__DIR__ . '/tmp', 0755, true);
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/core/app.php';
require_once __DIR__ . '/core/Controller.php';

spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/app/models/' . $className . '.php',
        __DIR__ . '/app/controller/Client/' . $className . '.php',
        __DIR__ . '/app/controller/admin/' . $className . '.php',
        __DIR__ . '/core/' . $className . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$app = new App();