<?php

class EmailService {
    // Configuration for Gmail (Requires App Password)
    private static $smtp_user = "your-email@gmail.com";
    private static $smtp_pass = "your-app-password"; 

    public static function sendAlert(string $to, string $subject, string $message): bool {
        // Since we don't have PHPMailer installed via Composer, 
        // this is a template for how it would be structured.
        // For now, we will log the email to a file to simulate sending.
        
        $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: $to | SUBJECT: $subject | MESSAGE: $message\n";
        file_put_contents(__DIR__ . '/../../mail_log.txt', $logEntry, FILE_APPEND);
        
        // In a real environment with Gmail:
        // 1. Download PHPMailer
        // 2. Use $mail->isSMTP(), $mail->Host = 'smtp.gmail.com', etc.
        
        // return mail($to, $subject, $message); // Standard PHP mail (requires SMTP in php.ini)
        return true; 
    }
}
?>
