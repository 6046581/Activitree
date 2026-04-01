<?php
class LocationsController
{
   private $model;

   public function __construct()
   {
      $this->model = new Locations();
   }

   public function getAllLocations($params, $data)
   {
      $limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 200;
      $offset = isset($_GET["offset"]) ? (int) $_GET["offset"] : 0;

      $rows = $this->model->getAllLocations($limit, $offset);
      return ["code" => 200, "data" => ["data" => $rows]];
   }

   public function getLocationById($params, $data)
   {
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      $row = $this->model->getLocationById($id);
      if (!$row) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      return ["code" => 200, "data" => ["data" => $row]];
   }
}
