<?php
$servername = "localhost";
$username = "root";     
$password = "";         
$dbname = "userdata";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    throw new mysqli_sql_exception($e->getMessage(), (int)$e->getCode());
}
?>