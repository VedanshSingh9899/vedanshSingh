<?php
header('Content-Type: application/json');

$host = '127.0.0.1';
$db = 'userdata';
$user = 'root'; // Change if needed
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

// Get POST data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$ph_number = isset($_POST['ph_number']) ? trim($_POST['ph_number']) : '';

if ($username === '' || $password === '' || $first_name === '' || $email === '' || $ph_number === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

// Check if username or email already exists
$stmt = $conn->prepare('SELECT username FROM pass WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'Username already exists']);
    exit();
}
$stmt->close();

$stmt = $conn->prepare('SELECT email FROM data WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'Email already registered']);
    exit();
}
$stmt->close();

// Insert into pass table
$stmt = $conn->prepare('INSERT INTO pass (username, pass) VALUES (?, ?)');
$stmt->bind_param('ss', $username, $password);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create user (pass)']);
    exit();
}
$stmt->close();

// Insert into data table
$stmt = $conn->prepare('INSERT INTO data (username, first_name, last_name, ph_number, email) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssis', $username, $first_name, $last_name, $ph_number, $email);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create user (data)']);
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

setcookie('session_token', $token, time() + 86400, '/', '', false, true);

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
