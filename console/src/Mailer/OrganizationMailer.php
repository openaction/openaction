<?php

namespace App\Mailer;

use App\Entity\Community\Contact;
use App\Entity\Community\ContactUpdate;
use App\Entity\Project;
use App\Proxy\DomainRouter;
use App\Repository\OrganizationRepository;
use App\Util\Uid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrganizationMailer
{
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private DomainRouter $domainRouter;
    private OrganizationRepository $repo;

    public function __construct(MailerInterface $m, TranslatorInterface $t, DomainRouter $domainRouter, OrganizationRepository $r)
    {
        $this->mailer = $m;
        $this->translator = $t;
        $this->domainRouter = $domainRouter;
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
                ->context(array_merge(
                    $this->createContext($project),
                    [
                        'redirect_url' => $this->domainRouter->generateRedirectUrl($project, 'register-confirm', Uid::toBase62($contact->getUuid()).'/'.$contact->getAccountConfirmToken()),
                    ]
                ))
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
                ->context(array_merge(
                    $this->createContext($project),
                    [
                        'redirect_url' => $this->domainRouter->generateRedirectUrl($project, 'reset-confirm', Uid::toBase62($contact->getUuid()).'/'.$contact->getAccountResetToken()),
                    ]
                ))
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
                ->context(array_merge(
                    $this->createContext($project),
                    [
                        'redirect_url' => $this->domainRouter->generateRedirectUrl($project, 'update-email-confirm', Uid::toBase62($contactUpdate->getUuid()).'/'.$contactUpdate->getToken()),
                    ]
                ))
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
                ->context(array_merge(
                    $this->createContext($project),
                    [
                        'redirect_url' => $this->domainRouter->generateRedirectUrl($project, 'unregister-confirm', Uid::toBase62($contactUpdate->getUuid()).'/'.$contactUpdate->getToken()),
                    ]
                ))
        );
    }

    private function createMessage(string $domain): TemplatedEmail
    {
        return (new TemplatedEmail())->from('no-reply@'.$domain);
    }

    private function createContext(Project $project): array
    {
        $logo = $project->getAppearanceLogoDark();
        $legalEmail = $project->getLegalGdprEmail() ?: 'contact@'.$project->getRootDomain()->getName();

        return [
            'locale' => $project->getWebsiteLocale(),
            'organization_name' => $project->getOrganization()->getName(),
            'primary_color' => $project->getAppearancePrimary(),
            'secondary_color' => $project->getAppearanceSecondary(),
            'third_color' => $project->getAppearanceThird(),
            'logo_pathname' => $logo ? $logo->getPathname() : null,
            'homepage_url' => $this->domainRouter->generateUrl($project, '/'),
            'website_enabled' => $project->isModuleEnabled('website'),
            'legal_gdpr_name' => $project->getLegalGdprName(),
            'legal_gdpr_address' => $project->getLegalGdprAddress(),
            'legal_email' => $legalEmail,
            'social_facebook_url' => $project->getSocialFacebook(),
            'social_twitter_url' => $project->getSocialTwitter(),
            'social_instagram_url' => $project->getSocialInstagram(),
            'social_linkedin_url' => $project->getSocialLinkedIn(),
            'social_youtube_url' => $project->getSocialYoutube(),
            'social_medium_url' => $project->getSocialMedium(),
            'social_telegram_url' => $project->getSocialTelegram() ? 'https://t.me/'.$project->getSocialTelegram() : null,
        ];
    }
}
