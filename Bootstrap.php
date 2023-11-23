<?php


spl_autoload_register(function ($class) {
    if (file_exists($class . '.php')) {
        include $class . '.php';
    } else if (file_exists('Classes/' . $class . '.php')) {
        include 'Classes/' . $class . '.php';
    } else if (file_exists('Validations/' . $class . '.php')) {
        include 'Validations/' . $class . '.php';
    } else if (file_exists(str_replace('\\', '/', $class) . '.php')) {
        include str_replace('\\', '/', $class) . '.php';
    }
});
// spl_autoload_register(function ($class) {
//     $baseDir = __DIR__; // Set your base directory

//     // Convert namespace separators to directory separators
//     $classFile = $baseDir . '/' . str_replace('\\', '/', $class) . '.php';

//     // Check if the file exists
//     if (file_exists($classFile)) {
//         include $classFile;
//     }
// });

define('DB_HOST', 'localhost');
define('DB_NAME', 'test');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
