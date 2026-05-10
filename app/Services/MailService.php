<?php
namespace App\Services;

class MailService {
    
    // TODO PRODUCTION: Change $from in /srv/http/school/app/Controllers/AuthController.php
    
    public static function send(string $from, string $to, string $subject, string $message): void {
        // LOCAL DEV: log instead of sending
        $log = "[" . date('Y-m-d H:i:s') . "]\n";
        $log .= "To: $to\nSubject: $subject\n\n$message\n\n";

        file_put_contents(
            __DIR__ . '/../../storage/mail_logs/mail.log',
            $log,
            FILE_APPEND
        );
        
        // PRODUCTION: Sending the verification email
        $headers = "From: $from\r\n";
        $headers .= "Reply-To: $from\r\n";
        $headers .= "Content-type: text/html\r\n";

        // Send now (To be comented out in the production)
        // mail($to, $subject, wordwrap($message, 70), $headers);
    }
}
