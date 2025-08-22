<?php
session_start();
header('Content-Type: application/json');
require_once 'redis_db.php';
require_once 'Sql_db.php';
require_once 'uslessTesting.php';

$registrationLast = new RegistrationFinalizer($conn, $redis, $_SESSION);
// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// 1. Check for a valid session first.
if (!isset($_SESSION['username'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User session not found. Please start the process again.']);
    exit;
}

// 2. Check for a valid JSON payload and the 'otp' key.
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON in request body.']);
    exit;
}

if (!isset($data['otp'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'OTP not provided in the request.']);
    exit;
}

$username = $_SESSION['username'];
$submitted_otp = $data['otp'];

// Retrieve the stored OTP from Redis
$redis_key = "otp:" . $username;
$stored_otp = $redis->get($redis_key);

if (!$stored_otp) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' =>'OTP has expired or does not exist. Please request a new one.']);
    exit;
}

// Verify the OTP
if (trim($submitted_otp) === trim($stored_otp)) {
    // Proceed with finalizing the registration
    try {
        $registrationLast->processRequest();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'An error occurred while finalizing registration. ' . $e->getMessage()]);
        exit;
    }
    $redis->del($redis_key); // Clean up the OTP after successful verification
    echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);;
} else {
    // Invalid OTP
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'The entered OTP is incorrect. Please try again.']);
}
?>
