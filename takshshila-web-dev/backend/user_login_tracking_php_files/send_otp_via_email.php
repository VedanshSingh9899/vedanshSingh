<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendOTPByEmail($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'your_email@gmail.com';                     //SMTP username
        $mail->Password   = 'your_password';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('your_email@gmail.com', 'Takshshila');
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = 'Your OTP is: <b>' . $otp . '</b>';
        $mail->AltBody = 'Your OTP is: ' . $otp;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Example usage (assuming you have the email and OTP from generate_otp.php)

// Include the generate_otp.php file
require 'generate_otp.php';

//After successful OTP generation
if (isset($email) && isset($otp)) {
    if (sendOTPByEmail($email, $otp)) {
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully to ' . $email]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP via email.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email or OTP not set.']);
}
?>