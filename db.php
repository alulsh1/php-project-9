<?php

require "vendor/autoload.php";

use PostgreSQLTutorial\Connection;
use PostgreSQLTutorial\PostgreSQLCreateTable;

try {
    $pdo = Connection::get()->connect();
    echo "A connection to the PostgreSQL database sever has been established successfully.";

    $tableCreator = new PostgreSQLCreateTable($pdo);
    $tables = $tableCreator->createTables();
} catch (\PDOException $e) {
    echo $e->getMessage();
}
