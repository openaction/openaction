<?php

namespace App\Form\Member\Model;

use App\Client\Model\ApiResource;

class UpdateGdprData
{
    public ?bool $settingsReceiveNewsletters = null;
    public ?bool $settingsReceiveSms = null;

    public static function createFromContact(ApiResource $contact): self
    {
        $self = new self();
        $self->settingsReceiveNewsletters = $contact->settingsReceiveNewsletters;
        $self->settingsReceiveSms = $contact->settingsReceiveSms;

        return $self;
    }

    public function createApiPayload(string $email): array
    {
        return [
            'email' => $email,
            'settingsReceiveNewsletters' => (bool) $this->settingsReceiveNewsletters,
            'settingsReceiveSms' => (bool) $this->settingsReceiveSms,
        ];
    }
}
