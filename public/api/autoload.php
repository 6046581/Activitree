<?php
spl_autoload_register(
    function ($class) {
        $folder = "classes";
        $basePath = __DIR__ . '/' . $folder . '/';

        $candidates = [
            $basePath . $class . '.php',
            $basePath . strtolower($class) . '.php'
        ];

        foreach ($candidates as $file) {
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }

        throw new Exception("Class $class not found in classes folder");
    }
);