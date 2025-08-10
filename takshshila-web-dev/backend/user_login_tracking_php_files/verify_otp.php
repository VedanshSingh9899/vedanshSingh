<?php
header('Content-Type: application/json');
require 'db.php';

define('OTP_EXPIRATION_MINUTES', 5); // Set OTP validity period

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email'], $data['otp'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required.']);
    exit;
}

$email = $data['email'];
$submitted_otp = $data['otp'];

// 1. Find the latest, unverified OTP for the user
$stmt = $pdo->prepare("SELECT otp_hash, created_at FROM otp_data WHERE user_email = ? AND is_verified = 0 ORDER BY created_at DESC LIMIT 1");
$stmt->execute();
$otp_record = $stmt->fetch();

if (!$otp_record) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'No pending OTP found. Please request a new one.']);
    exit;
}

// 2. Check if the OTP has expired
$otp_timestamp = strtotime($otp_record['created_at']);
if (time() - $otp_timestamp > (OTP_EXPIRATION_MINUTES * 60)) {
    // OTP is expired, delete it from the database.
    $stmt_delete = $pdo->prepare("DELETE FROM otp_data WHERE otp_hash = ?");
    $stmt_delete->execute([$otp_record['otp_hash']]);

    http_response_code(410); // 410 Gone
    echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
    exit;
}

// 3. Securely verify the OTP. If it's correct, delete it to prevent reuse.
if (password_verify($submitted_otp, $otp_record['otp_hash'])) {
    // OTP is correct, delete it.
    $stmt_delete = $pdo->prepare("DELETE FROM otp_data WHERE otp_hash = ?");
    $stmt_delete->execute([$otp_record['otp_hash']]);
    echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
}
?>
