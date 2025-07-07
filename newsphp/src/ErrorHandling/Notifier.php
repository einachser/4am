<?php

namespace App\ErrorHandling;

class Notifier
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function sendErrorNotification(string $errorMessage)
    {
        if (!($this->config["enabled"] ?? false)) {
            return; // Email notification is not enabled
        }

        $to = $this->config["to"] ?? "";
        $subject = $this->config["subject"] ?? "Script Error Notification";
        $from = $this->config["from"] ?? "no-reply@example.com";

        if (empty($to)) {
            // Log: "Email recipient not configured for error notification."
            return;
        }

        $headers = "From: " . $from . "\r\n" .
                   "Reply-To: " . $from . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        $message = "An error occurred in the PHP script:\n\n";
        $message .= $errorMessage;
        $message .= "\n\nTimestamp: " . date("Y-m-d H:i:s");

        // Using mail() function. For production, consider a more robust SMTP library.
        if (!mail($to, $subject, $message, $headers)) {
            // Log: "Failed to send error notification email to {$to}"
        }
    }
}


