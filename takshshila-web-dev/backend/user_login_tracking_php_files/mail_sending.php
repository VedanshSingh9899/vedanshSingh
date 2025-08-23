<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailSender
{
    private $mail;

    public function __construct(PHPMailer $mail)
    {
        $this->mail = $mail;
    }

    public function sendOtpEmail($email, $otp, $username)
    {
        try {

            // SMTP settings
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;                    //Enable verbose debug output... changed DEBUG_OFF to DEBUG_SERVER
            $this->mail->isSMTP();                                          //Send using SMTP
            $this->mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $this->mail->SMTPAuth   = true;                                 //Enable SMTP authentication
            $this->mail->Username   = 'vedanshpratapsingh@gmail.com';       //SMTP username
            $this->mail->Password   = 'urba ytmc eaim nufs';                //SMTP password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          //Enable implicit TLS encryption
            $this->mail->Port       = 465;                                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`


            $this->mail->setFrom('vedanshpratapsingh@gmail.com', 'Takshshila');
            $this->mail->addAddress($email);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Your OTP Code';

            $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
            $otp = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');

            $this->mail->Body = <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Takshshila</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #1bb9f8, #d7fd4f);
            color:white;
            padding: 25px;
            text-align: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .content {
            padding: 30px;
            color: #333;
            line-height: 1.6;
        }
        
        .greeting {
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .user-name {
            font-weight: bold;
            color: #1a3a8f;
        }
        
        .otp-container {
            background-color: #f0f6ff;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .otp-code {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #1a3a8f;
            text-align: center;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }
        .support {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
        }
        
        .contact {
            margin: 15px 0;
            padding: 12px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        
        .email {
            color: #2d5cc0;
            font-weight: bold;
        }
        
        .footer {
            background-color: #f0f0f0;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .unsubscribe {
            margin-top: 10px;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">Takshshila</div>
        </div>
        
        <div class="content">
            <div class="greeting">
                <p>Hello there,</p>
                <p>Enter this code within the next 5 minutes to complete your sign-up.</p>
            </div>
            
            <p>Dear <span class="user-name">$username</span>,</p>
            
            <p>Your email verification OTP is</p>
            
            <div class="otp-container">
                <p class="otp-code">$otp</p>
            </div>
            <div class="support">
                <p>If you encounter any issues, please feel free to contact us. If you did not request this email, kindly disregard it.</p>
                
                <div class="contact">
                    Email: <span class="email">support@takshshila.com</span>
                </div>
                
                <p>Takshshila Team</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; 2023 Takshshila. All rights reserved.</p>
            <p class="unsubscribe">If these emails get annoying, please feel free to unsubscribe</p>
        </div>
    </div>
</body>
</html>
EOT;
            $this->mail->AltBody = "Your email verification OTP is: $otp";

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}"); // It's good practice to log the error.
            return false;
        }
    }
}
?>