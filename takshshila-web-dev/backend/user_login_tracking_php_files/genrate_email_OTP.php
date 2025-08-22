<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Prevent any accidental output
ob_start();

// Set JSON header first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// Start session
session_start();

// Define OTP expiration time in seconds (5 minutes)
define('OTP_EXPIRATION_SECONDS', 300);

try {
    // Include Redis connection
    require_once 'redis_db.php';
    require_once 'mail_sending.php';
    require __DIR__ . '/../vendor/autoload.php'; // Include mail sending script
    $mail = new PHPMailer(true); // Initialize PHPMailer

    $mail_sender = new EmailSender($mail); // Create an instance of MailSender
    
    // Get username from session
    $username = $_SESSION['username'] ?? null;
    $email = $username ? $redis->hget('user:' . $username, 'email') : null;

    // Generate a cryptographically secure 6-digit OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Store the OTP in Redis with expiration
    $redis_key = "otp:" . $username;
    $redis->setex($redis_key, OTP_EXPIRATION_SECONDS, $otp);
    
    // Return single JSON response
    $response = [
        'success' => true,
        'message' => 'OTP generated and stored successfully. It will expire in 5 minutes.',
        'username' => $username,
        'expires_in' => OTP_EXPIRATION_SECONDS
    ];
    $mail_sender->sendOtpEmail($email, $otp, $username);
    
    // Clear any buffered output and send JSON
    ob_clean();
    echo json_encode($response);  
} catch (RedisException $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Redis error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush();
?>