<?php

namespace App\Form\Model;

use App\Client\Model\ApiResource;

class UpdateGdprData
{
    public ?bool $settingsReceiveNewsletters = null;
    public ?bool $settingsReceiveSms = null;
    public ?bool $settingsReceiveCalls = null;
    public array $settingsByProject = [];

    public static function createFromContact(ApiResource $contact): self
    {
        $self = new self();
        $self->settingsReceiveNewsletters = $contact->settingsReceiveNewsletters;
        $self->settingsReceiveSms = $contact->settingsReceiveSms;
        $self->settingsReceiveCalls = $contact->settingsReceiveCalls;
        $self->settingsByProject = $contact->settingsByProject;

        return $self;
    }

    public function createApiPayload(string $email): array
    {
        return [
            'email' => $email,
            'settingsReceiveNewsletters' => (bool) $this->settingsReceiveNewsletters,
            'settingsReceiveSms' => (bool) $this->settingsReceiveSms,
            'settingsReceiveCalls' => (bool) $this->settingsReceiveCalls,
            'settingsByProject' => $this->settingsByProject,
        ];
    }
}
