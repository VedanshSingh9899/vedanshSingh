<?php
require 'db.php';

$headers = getallheaders();
$token = str_replace("Bearer ", "", $headers['Authorization'] ?? '');

if (!$token) {
    echo json_encode(["valid" => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT users.id, users.username FROM user_sessions
                       JOIN users ON users.id = user_sessions.user_id
                       WHERE session_token = ? AND is_active = 1");
$stmt->execute([$token]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode(["valid" => true, "user" => $user]);
} else {
    echo json_encode(["valid" => false]);
}
?>
