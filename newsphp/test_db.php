<?php

require_once __DIR__ . "/autoload.php";

use App\Config\ConfigLoader;
use App\Storage\DatabaseManager;

$config = new ConfigLoader(__DIR__ . "/config.php");

try {
    $dbManager = new DatabaseManager($config->get("database", []));
    echo "Database connection successful!\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}

?>

