<?php

namespace App\Bridge\Twilio\Consumer;

interface TwilioMessageDataInterface
{
    public function setData(string $messageId, object $data);
}
