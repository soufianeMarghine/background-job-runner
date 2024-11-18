<?php

namespace App\Jobs;

class SendEmailJob
{
    public function execute($email, $message)
    {
        // Simulate sending email
        echo "Sending email to: " . $email . "\n";
        echo "Message: " . $message . "\n";
    }
}
