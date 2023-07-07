<?php

namespace App\Community\Automation\EmailTrigger;

use App\Community\Automation\EmailAutomationMatcher;
use App\Community\EmailAutomationSender;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Organization;
use App\Repository\Community\EmailAutomationRepository;

abstract class AbstractEmailTrigger implements EmailTriggerInterface
{
    private EmailAutomationRepository $repository;
    private EmailAutomationMatcher $matcher;
    private EmailAutomationSender $sender;

    /**
     * @return EmailAutomation[]
     */
    protected function findMatchingAutomationsFor(string $trigger, Organization $organization, ?Contact $contact): array
    {
        $automations = $this->repository->findBy(
            ['organization' => $organization, 'trigger' => $trigger, 'enabled' => true],
            ['weight' => 'ASC'],
        );

        $matching = [];
        foreach ($automations as $automation) {
            if ($this->matcher->matches($automation, $contact)) {
                $matching[] = $automation;
            }
        }

        return $matching;
    }

    protected function sendAutomationEmail(EmailAutomation $automation, ?Contact $contact, array $additionalVariables = []): bool
    {
        return $this->sender->send($automation, $contact, $additionalVariables);
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setRepository(EmailAutomationRepository $repository)
    {
        $this->repository = $repository;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setMatcher(EmailAutomationMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setSender(EmailAutomationSender $sender)
    {
        $this->sender = $sender;
    }
}
