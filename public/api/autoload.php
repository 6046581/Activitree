<?php
spl_autoload_register(
    function ($class) {
        $folder = "classes";

        $file = __DIR__ . '/' . $folder . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }

        throw new Exception("Class $class not found in $file");
    }
);