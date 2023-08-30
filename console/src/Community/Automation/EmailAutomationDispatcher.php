<?php

namespace App\Community\Automation;

use App\Community\Automation\EmailTrigger\EmailTriggerInterface;
use App\Entity\Community\Contact;
use App\Entity\Organization;

class EmailAutomationDispatcher
{
    /**
     * @var EmailTriggerInterface[]
     */
    private array $handlers = [];

    public function __construct(iterable $handlers)
    {
        /** @var EmailTriggerInterface $handler */
        foreach ($handlers as $handler) {
            $this->handlers[$handler->getTrigger()] = $handler;
        }
    }

    public function dispatch(string $trigger, Organization $organization, ?Contact $contact, $subject)
    {
        if (isset($this->handlers[$trigger])) {
            $this->handlers[$trigger]->handle($organization, $contact, $subject);
        }
    }
}
