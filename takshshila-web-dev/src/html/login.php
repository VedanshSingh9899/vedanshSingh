<?php
// Start session and set headers
header('Content-Type: application/json');

// Database config
$host = 'localhost';
$db = 'userdata';
$user = 'root'; // Change if needed
$pass = '';

// Connect to DB
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

// Get POST data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing username or password']);
    exit();
}

// Check credentials
$stmt = $conn->prepare('SELECT pass FROM pass WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    exit();
}
$stmt->bind_result($db_pass);
$stmt->fetch();
if ($db_pass !== $password) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    exit();
}
$stmt->close();

// Generate session token
$token = bin2hex(random_bytes(32));
$now = date('Y-m-d H:i:s');
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

// Store session in user_sessions table
$stmt = $conn->prepare('REPLACE INTO user_sessions (username, session_token, last_active, user_agent, is_active) VALUES (?, ?, ?, ?, 1)');
$stmt->bind_param('ssss', $username, $token, $now, $user_agent);
$stmt->execute();
$stmt->close();

// Set session token as cookie (HttpOnly, Secure if using HTTPS)
setcookie('session_token', $token, time() + 86400, '/', '', false, true);

// Optionally, fetch user info from data table
$stmt = $conn->prepare('SELECT first_name, last_name, email, ph_number FROM data WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $ph_number);
$stmt->fetch();
$stmt->close();

// Success response
echo json_encode([
    'success' => true,
    'username' => $username,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
    'ph_number' => $ph_number,
    'session_token' => $token
]);

$conn->close();
