<?php
session_start();
require_once 'sql_db.php';

$session_id = session_id();
$count = 0;

// Check if the session ID exists in the database
$stmt = $conn->prepare("SELECT COUNT(*) FROM user_sessions WHERE session_id = ?");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    //for localhost
    //header("Location: ../../src/html/Homes.html");
    //for server
    header("Location: /takshshila-web-dev/src/html/Homes.html");
    exit();
} else {
    header("Location: /takshshila-web-dev/src/html/LoginMobile.html");
    exit();
}
?>