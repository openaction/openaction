<?php

namespace App\Form\Community\Model;

use App\Entity\Community\EmailAutomation;
use App\Entity\Website\Form;
use Symfony\Component\Validator\Constraints as Assert;

class EmailAutomationMetaData
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Choice([EmailAutomation::TRIGGER_NEW_CONTACT, EmailAutomation::TRIGGER_NEW_FORM_ANSWER])]
    public ?string $trigger = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public ?string $subject = null;

    #[Assert\Length(max: 150)]
    public ?string $preview = null;

    #[Assert\NotBlank]
    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 150)]
    public ?string $fromEmail = null;

    #[Assert\Length(max: 150)]
    public ?string $fromName = null;

    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 150)]
    public ?string $replyToEmail = null;

    #[Assert\Length(max: 150)]
    public ?string $replyToName = null;

    public ?string $toEmailType = null;

    #[Assert\Email(mode: 'strict')]
    #[Assert\Length(max: 150)]
    public ?string $toEmail = null;

    #[Assert\Choice(['', EmailAutomation::TYPE_CONTACT, EmailAutomation::TYPE_MEMBER])]
    public ?string $typeFilter = null;

    public ?Form $formFilter = null;

    public static function createFromAutomation(EmailAutomation $automation): self
    {
        $self = new self();
        $self->name = $automation->getName();
        $self->trigger = $automation->getTrigger();
        $self->subject = $automation->getSubject();
        $self->preview = $automation->getPreview();
        $self->fromEmail = $automation->getFromEmail();
        $self->fromName = $automation->getFromName();
        $self->replyToEmail = $automation->getReplyToEmail();
        $self->replyToName = $automation->getReplyToName();
        $self->toEmailType = $automation->getToEmail() ? 'specific' : 'everyone';
        $self->toEmail = $automation->getToEmail();
        $self->typeFilter = $automation->getTypeFilter();
        $self->formFilter = $automation->getFormFilter();

        return $self;
    }
}
