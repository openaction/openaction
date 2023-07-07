<?php

namespace App\Bridge\Sendgrid\Consumer;

use SendGrid\Mail\Mail;

final class SendgridMessage
{
    private Mail $mail;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }
}
