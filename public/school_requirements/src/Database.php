<?php

class Database
{
   private static ?Database $instance = null;

   private function __construct() {}

   public static function getInstance(): Database
   {
      if (self::$instance === null) {
         self::$instance = new self();
      }

      return self::$instance;
   }
}
