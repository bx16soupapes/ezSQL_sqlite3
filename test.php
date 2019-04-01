<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("ez_sqlite3.php");

$db = new ezSQL_sqlite3("./","sqlite-database.db");


$results = $db->get_results("select * from horarios",ARRAY_A);

echo "<pre>";
print_r($results);
echo "</pre>";

unset($db);

?>
