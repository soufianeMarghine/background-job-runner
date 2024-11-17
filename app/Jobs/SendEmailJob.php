<?php
namespace App\Jobs;

class SendEmailJob
{
    public function execute($email)
    {
        // Simulate sending email
        echo "Sending email to: " . $email;
    }
}
