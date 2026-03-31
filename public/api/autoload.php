<?php
spl_autoload_register(function ($class) {
   $file = __DIR__ . "/classes/" . $class . ".php";
   if (file_exists($file)) {
      require_once $file;
      return;
   }

   throw new Exception("Class $class not found in classes folder");
});
