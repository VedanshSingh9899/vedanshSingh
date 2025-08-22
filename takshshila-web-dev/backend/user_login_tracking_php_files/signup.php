<?php
header('Content-Type: application/json');
require_once 'redis_db.php';
require_once 'Sql_db.php';
require_once 'userValidatior.php';

$lifetime = 60 * 60 * 24 * 7; // 7 days
session_set_cookie_params($lifetime);

// OR with more control:
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'domain' => '',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443),
    'httponly' => true,
    'samesite' => 'Lax'
]);

ini_set('session.gc_maxlifetime', $lifetime);

session_start();

$validator = new UserValidator($conn, $redis);

$result = $validator->isUserUnique($_POST['username'], $_POST['email'], $_POST['ph_number']);

if (!$result['status']) {
    http_response_code(409); // Conflict
    echo json_encode(['success' => false, 'message' => $result['message']]);
    exit;
}

// --- Main Script Logic ---

$firstName = trim($_POST["first_name"] ?? '');
$lastName = trim($_POST["last_name"] ?? '');
$email = trim($_POST["email"] ?? '');
$phone = trim($_POST["ph_number"] ?? '');
$username = trim($_POST["username"] ?? '');
$password = $_POST["password"] ?? '';
$confirmPassword = $_POST["confirmPassword"] ?? '';

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$array = [
    'username' => $username,
    'first_name' => $firstName, 
    'last_name' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'password' => $passwordHash,
    'session_id' => $_SESSION['session_id'] ?? session_id(),
    'created_at' => date('Y-m-d H:i:s'),
];
$hash_result = $redis->hmset('user:'.$username,$array);
if ($hash_result) {
    $_SESSION['username'] = $username;
    echo json_encode(['success' => true, 'message' => 'User data stored successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to store user data.']);
}
?>