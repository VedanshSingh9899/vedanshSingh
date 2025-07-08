<?php
require 'db.php';

$headers = getallheaders();
$token = str_replace("Bearer ", "", $headers['Authorization'] ?? '');

$stmt = $pdo->prepare("UPDATE user_sessions SET is_active = 0 WHERE session_token = ?");
$stmt->execute([$token]);

echo json_encode(["success" => true]);
