<?php

namespace App\Mailer;

use App\Entity\Community\Contact;
use App\Entity\Community\ContactUpdate;
use App\Entity\Project;
use App\Repository\OrganizationRepository;
use App\Util\Uid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrganizationMailer
{
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private OrganizationRepository $repo;

    public function __construct(MailerInterface $m, TranslatorInterface $t, OrganizationRepository $r)
    {
        $this->mailer = $m;
        $this->translator = $t;
        $this->repo = $r;
    }

    public function sendRegistrationConfirm(Project $project, Contact $contact)
    {
        if (!$this->repo->useCredits($project->getOrganization(), 1, 'register')) {
            return;
        }

        $this->mailer->send(
            $this->createMessage($project->getRootDomain()->getName())
                ->to($contact->getEmail())
                ->subject($this->translator->trans('community.register.subject', [], 'emails', $project->getWebsiteLocale()))
                ->htmlTemplate('emails/community/account_register.html.twig')
                ->context([
                    'current_organization' => $project->getOrganization(),
                    'project' => $project,
                    'contact' => $contact,
                    'reference' => Uid::toBase62($contact->getUuid()).'/'.$contact->getAccountConfirmToken(),
                ])
        );
    }

    public function sendResetConfirm(Project $project, Contact $contact)
    {
        if (!$this->repo->useCredits($project->getOrganization(), 1, 'reset')) {
            return;
        }

        $this->mailer->send(
            $this->createMessage($project->getRootDomain()->getName())
                ->to($contact->getEmail())
                ->subject($this->translator->trans('community.reset.subject', [], 'emails', $project->getWebsiteLocale()))
                ->htmlTemplate('emails/community/account_reset.html.twig')
                ->context([
                    'current_organization' => $project->getOrganization(),
                    'project' => $project,
                    'contact' => $contact,
                    'reference' => Uid::toBase62($contact->getUuid()).'/'.$contact->getAccountResetToken(),
                ])
        );
    }

    public function sendEmailChangedConfirm(Project $project, ContactUpdate $contactUpdate)
    {
        if (!$this->repo->useCredits($project->getOrganization(), 1, 'contact_update_email')) {
            return;
        }

        $contact = $contactUpdate->getContact();

        $this->mailer->send(
            $this->createMessage($project->getRootDomain()->getName())
                ->to($contact->getEmail())
                ->subject($this->translator->trans('community.update_email.subject', [], 'emails', $project->getWebsiteLocale()))
                ->htmlTemplate('emails/community/account_update_email.html.twig')
                ->context([
                    'current_organization' => $project->getOrganization(),
                    'contact' => $contact,
                    'project' => $project,
                    'reference' => Uid::toBase62($contactUpdate->getUuid()).'/'.$contactUpdate->getToken(),
                ])
        );
    }

    public function sendUnregisterConfirm(Project $project, ContactUpdate $contactUpdate)
    {
        $contact = $contactUpdate->getContact();

        $this->mailer->send(
            $this->createMessage($project->getRootDomain()->getName())
                ->to($contact->getEmail())
                ->subject($this->translator->trans('community.unregister.subject', [], 'emails', $project->getWebsiteLocale()))
                ->htmlTemplate('emails/community/account_unregister.html.twig')
                ->context([
                    'current_organization' => $project->getOrganization(),
                    'contact' => $contact,
                    'project' => $project,
                    'reference' => Uid::toBase62($contactUpdate->getUuid()).'/'.$contactUpdate->getToken(),
                ])
        );
    }

    private function createMessage(string $domain): TemplatedEmail
    {
        return (new TemplatedEmail())->from('no-reply@'.$domain);
    }
}
