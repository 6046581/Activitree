<?php

abstract class AbstractModel
{
   protected $conn;

   public function __construct($database = null)
   {
      if ($database === null) {
         $database = Database::getInstance();
      }

      $this->conn = $database->getConnection();
   }
}
