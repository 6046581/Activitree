<?php

abstract class BaseRepository implements Repository
{
   protected Database $db;

   public function __construct()
   {
      $this->db = Database::getInstance();
   }
}
