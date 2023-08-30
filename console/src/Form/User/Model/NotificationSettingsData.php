<?php

namespace App\Form\User\Model;

use App\Entity\Model\NotificationSettings;
use Symfony\Component\Validator\Constraints as Assert;

class NotificationSettingsData
{
    #[Assert\All([new Assert\Choice(['callback' => [NotificationSettings::class, 'getAllEvents']])])]
    public ?array $events = [];

    public function __construct(NotificationSettings $settings)
    {
        $this->events = $settings->toArray();
    }
}
