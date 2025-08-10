<?php
header('Content-Type: application/json');
require 'db.php';

// In a real application, you would get the email from a logged-in user's session
// or from a form post during a password reset flow.
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A valid email is required.']);
    exit;
}

$email = $data['email'];

// 1. Generate a cryptographically secure OTP
// We use random_int for security. str_pad ensures it's always 6 digits.
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// 2. Hash the OTP for secure storage
// We use the same strong hashing as for passwords.
$otp_hash = password_hash($otp, PASSWORD_DEFAULT);

// 3. Store the hashed OTP in the database
// We delete any previous, unverified OTPs for this user first to ensure only one is valid.
try {
    // Using the recommended PDO connection from db.php
    $pdo->beginTransaction();

    $stmt_delete = $pdo->prepare("DELETE FROM otp_data WHERE user_email = ? AND is_verified = 0");
    $stmt_delete->execute([$email]);

    $stmt_insert = $pdo->prepare("INSERT INTO otp_data (user_email, otp_hash) VALUES (?, ?)");
    $stmt_insert->execute([$email, $otp_hash]);

    $pdo->commit();

    // For demonstration, we return the OTP. In production, NEVER do this.
    echo json_encode(['success' => true, 'message' => 'OTP generated successfully.', 'otp_for_testing' => $otp]);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
