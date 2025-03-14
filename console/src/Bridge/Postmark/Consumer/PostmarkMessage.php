<?php

namespace App\Bridge\Postmark\Consumer;

use App\Bridge\Postmark\Model\Mail;

final class PostmarkMessage
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
