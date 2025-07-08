<?php
session_start();
header('Content-Type: application/json');
require 'db.php'; // Your DB connection

if (!isset($_POST['username'], $_POST['password'])) {
    echo json_encode(["success" => false, "message" => "Missing credentials."]);
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

// 1. Verify credentials
$stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // 2. Check if user is already logged in
    $stmt = $pdo->prepare("SELECT * FROM user_sessions WHERE user_id = ? AND is_active = 1");
    $stmt->execute([$user['id']]);
    $existing = $stmt->fetch();

    if ($existing) {
        echo json_encode(["success" => false, "message" => "You're already logged in on another device."]);
        exit;
    }

    // 3. Create session token
    $token = bin2hex(random_bytes(32));
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $now = date("Y-m-d H:i:s");

    // 4. Insert or update session
    $stmt = $pdo->prepare("REPLACE INTO user_sessions (user_id, session_token, last_active, user_agent, is_active)
                           VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([$user['id'], $token, $now, $agent]);

    // 5. Return token to JS
    echo json_encode(["success" => true, "token" => $token]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials."]);
}

// jabbhi user longin krega, token generate hoga
// token ko user ke session me store krna hoga or ye cheez local storage me store krni hogi
?>