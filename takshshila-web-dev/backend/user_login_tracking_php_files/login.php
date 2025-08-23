<?php

header('Content-Type: application/json');
require_once 'Sql_db.php';

$lifetime = 60 * 60 * 24 * 7; // 7 days
session_set_cookie_params($lifetime);

ini_set('session.gc_maxlifetime', $lifetime);

// OR with more control:
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'domain' => '',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// 1. Get and validate JSON or form input
$data = json_decode(file_get_contents('php://input'), true);

$username = $_POST['username'];
$submittedPassword = $_POST['password'];

if (empty($username) || empty($submittedPassword)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password cannot be empty.']);
    exit;
}

try {
    // 2. Fetch user ID and hashed password from the database
    $stmt = $conn->prepare("SELECT uid, pass FROM user_password WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // 3. Check if user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $storedPasswordHash = $user['pass'];
        $uid = $user['uid'];

        // 4. Verify the password
        if (password_verify($submittedPassword, $storedPasswordHash)) {
            // 5. Password is correct, start session management
            session_regenerate_id(true); // Prevent session fixation

            $_SESSION['uid'] = $uid;
            $_SESSION['username'] = $username;

            // 6. Save session to the database
            $sessionId = session_id();
            $sessionStmt = $conn->prepare(
                "INSERT INTO user_sessions (uid, session_id, last_active) VALUES (?, ?, NOW()) 
                 ON DUPLICATE KEY UPDATE session_id = VALUES(session_id), last_active = NOW()"
            );
            $sessionStmt->bind_param("is", $uid, $sessionId);
            //$redis->set("session:$username", $sessionId, 600); // Store in Redis for 1 hour
            $sessionStmt->execute();
            $sessionStmt->close();

            // 7. Send success response with session token for frontend compatibility
            echo json_encode(['success' => true, 'message' => 'Login successful.', 'token' => $sessionId]);
            error_log("User $username logged in successfully.");
        } else {
            // Password incorrect
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
            error_log("Failed login attempt for user $username: Incorrect password.");
        }
    } else {
        // User not found
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'username does not exist.']);
        error_log("Failed login attempt for user $username: User not found.");
    }

    $stmt->close();

} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    // In production, log the error instead of echoing it.
    echo json_encode(['success' => false, 'message' => 'A server error occurred. Please try again later.']);
    error_log("Database error during login for user $username: " . $e->getMessage());
    exit;
}
?>