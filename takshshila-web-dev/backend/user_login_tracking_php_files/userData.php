<?php
session_start();
require_once 'sql_db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated.']);
    exit();
}
$sessionID = $_SESSION['session_id'] ?? session_id();
error_log($sessionID);
$userData = [];

try {
    $uid_stmt = $conn->prepare('SELECT uid FROM user_sessions WHERE session_id = ?');
    $uid_stmt->bind_param('i', $sessionID);
    $uid_stmt->execute();
    $uid_result = $uid_stmt->get_result();
    $uid_row = $uid_result->fetch_assoc();
    $uid = $uid_row['uid'] ?? null;
    error_log($uid); // Use null coalescing operator for cleaner check

    if ($uid) {
        $stmt = $conn->prepare('SELECT first_name, last_name FROM user_data WHERE uid = ?');
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $uid_stmt->close();

        $usernameSTMT = $conn->prepare('SELECT username FROM user_password WHERE uid = ?');
        $usernameSTMT->bind_param('i', $uid);
        $usernameSTMT->execute();
        $usernameResult = $usernameSTMT->get_result();
        $usernameRow = $usernameResult->fetch_assoc();
        $username = $usernameRow['username'] ?? null;
        $usernameSTMT->close();

        if ($user) {
            $userData = [
                "first_name" => $user['first_name'],
                "last_name" => $user['last_name'],
                "username" => $username,
            ];
        } else {
            http_response_code(404); // Not Found
            $userData = ['error' => 'User data not found.'];
        }
    } else {
        http_response_code(404); // Not Found
        $userData = ['error' => 'User not found.'];
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    $userData = ['error' => 'A database error occurred.'];
}
echo json_encode($userData);
?>