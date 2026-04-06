<?php

require_once __DIR__ . "/src/Database.php";
require_once __DIR__ . "/src/Repository.php";
require_once __DIR__ . "/src/BaseRepository.php";
require_once __DIR__ . "/src/Users.php";
require_once __DIR__ . "/src/Activities.php";

$users = new Users();
$activities = new Activities();

echo "Users type: " . $users->getType() . "\n";
echo "Activities type: " . $activities->getType() . "\n";

$dbA = Database::getInstance();
$dbB = Database::getInstance();

echo $dbA === $dbB ? "Singleton works\n" : "Singleton failed\n";
