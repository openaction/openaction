<?php

namespace App\Mailer;

use App\Cdn\CdnRouter;
use App\Entity\Billing\Order;
use App\Entity\Billing\Quote;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Registration;
use App\Entity\Upload;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlatformMailer
{
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private CdnRouter $cdnRouter;
    private string $senderEmail;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, CdnRouter $cdnRouter, string $senderEmail)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->cdnRouter = $cdnRouter;
        $this->senderEmail = $senderEmail;
    }

    public function sendRegistrationVerify(Registration $registration)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($registration->getEmail())
                ->subject($this->translator->trans('transactional.registration.verify.subject', [], 'emails', $registration->getLocale()))
                ->htmlTemplate('emails/security/registration/verify.html.twig')
                ->context($this->createContext([
                    'locale' => $registration->getLocale(),
                    'registration_uuid' => $registration->getUuid()->toRfc4122(),
                    'registration_token' => $registration->getToken(),
                ], $registration->getOrganization()))
        );
    }

    public function sendRegistrationWelcome(User $user)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans('transactional.registration.welcome.subject', [], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/security/registration/welcome.html.twig')
                ->context($this->createContext([
                    'locale' => $user->getLocale(),
                    'name' => $user->getFullName(),
                ]))
        );
    }

    public function sendOrganizationInvite(Organization $organization, Registration $registration, User $author)
    {
        $subject = $this->translator->trans(
            'transactional.invite.subject',
            ['%author%' => $author->getFullName()],
            'emails',
            $registration->getLocale()
        );

        $this->mailer->send(
            $this->createMessage()
                ->to($registration->getEmail())
                ->subject($subject)
                ->htmlTemplate('emails/console/organization/invite_new.html.twig')
                ->context($this->createContext([
                    'locale' => $registration->getLocale(),
                    'registration_uuid' => $registration->getUuid()->toRfc4122(),
                    'registration_token' => $registration->getToken(),
                    'organization_name' => $organization->getName(),
                    'author_name' => $author->getFullName(),
                ], $organization))
        );
    }

    public function sendOrganizationInviteToRegisteredUser(Organization $organization, User $invited, User $author)
    {
        $subject = $this->translator->trans(
            'transactional.invite.subject',
            ['%author%' => $author->getFullName()],
            'emails',
            $invited->getLocale()
        );

        $this->mailer->send(
            $this->createMessage()
                ->to($invited->getEmail())
                ->subject($subject)
                ->htmlTemplate('emails/console/organization/invite_registered.html.twig')
                ->context($this->createContext([
                    'locale' => $invited->getLocale(),
                    'organization_uuid' => $organization->getUuid()->toRfc4122(),
                    'organization_name' => $organization->getName(),
                    'author_name' => $author->getFullName(),
                ], $organization))
        );
    }

    public function sendForgottenPasswordRequest(User $user)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans('transactional.forgot_password.request.subject', [], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/security/forgot-password/request.html.twig')
                ->context($this->createContext([
                    'locale' => $user->getLocale(),
                    'secret' => $user->getSecretResetPassword(),
                ]))
        );
    }

    public function sendPasswordUpdated(User $user)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans('transactional.forgot_password.updated.subject', [], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/security/forgot-password/updated.html.twig')
                ->context($this->createContext([
                    'locale' => $user->getLocale(),
                ]))
        );
    }

    public function sendNotificationNewProject(User $user, Organization $organization, Project $project)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans('transactional.notification.new_project.subject', [], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/console/notification/new_project.html.twig')
                ->context($this->createContext([
                    'locale' => $user->getLocale(),
                    'project_name' => $project->getName(),
                    'organization_name' => $organization->getName(),
                ], $organization))
        );
    }

    public function sendNotificationSubscriptionExpiration(User $user, Organization $organization)
    {
        $daysLeft = (new \DateTime())->diff($organization->getSubscriptionCurrentPeriodEnd())->days;

        $subject = 'transactional.notification.subscription_expiration.subject_early';
        if ($daysLeft <= 10) {
            $subject = 'transactional.notification.subscription_expiration.subject_late';
        }

        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans($subject, [
                    '%organization%' => $organization->getName(),
                    '%days_left%' => $daysLeft,
                ], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/console/notification/subscription_expiration.html.twig')
                ->context($this->createContext([
                    'locale' => $user->getLocale(),
                    'organization_name' => $organization->getName(),
                    'days_left' => $daysLeft,
                ], $organization))
        );
    }

    public function sendNotificationNewInvoice(Order $order, string $file)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($order->getOrganization()->getBillingEmail())
                ->subject($this->translator->trans('transactional.notification.new_invoice.subject', [
                    '%number%' => $order->getInvoiceNumber(),
                    '%organization%' => $order->getOrganization()->getName(),
                ], 'emails', $order->getRecipient()->getLocale()))
                ->htmlTemplate('emails/console/notification/new_invoice.html.twig')
                ->context($this->createContext([
                    'organization_name' => $order->getOrganization()->getName(),
                    'locale' => $order->getRecipient()->getLocale(),
                ], $order->getOrganization()))
                ->attach(file_get_contents($file), pathinfo($file, PATHINFO_FILENAME).'.pdf', 'application/pdf')
        );
    }

    public function sendNotificationNewQuote(Quote $quote, string $file)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($quote->getOrganization()->getBillingEmail())
                ->subject($this->translator->trans('transactional.notification.new_quote.subject', [
                    '%number%' => $quote->getNumber(),
                    '%organization%' => $quote->getOrganization()->getName(),
                ], 'emails', $quote->getRecipient()->getLocale()))
                ->htmlTemplate('emails/console/notification/new_quote.html.twig')
                ->context($this->createContext([
                    'organization_name' => $quote->getOrganization()->getName(),
                    'locale' => $quote->getRecipient()->getLocale(),
                ], $quote->getOrganization()))
                ->attach(file_get_contents($file), pathinfo($file, PATHINFO_FILENAME).'.pdf', 'application/pdf')
        );
    }

    public function sendNotificationExportFinished(string $locale, string $email, Organization $orga, Upload $upload)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($email)
                ->subject($this->translator->trans('transactional.notification.export.subject', [
                    '%organization%' => $orga->getName(),
                ], 'emails', $locale))
                ->htmlTemplate('emails/console/notification/export.html.twig')
                ->context($this->createContext([
                    'organization_uuid' => $orga->getUuid()->toRfc4122(),
                    'organization_name' => $orga->getName(),
                    'locale' => $locale,
                    'export_pathname' => $upload->getPathname(),
                ], $orga))
        );
    }

    private function createMessage(): TemplatedEmail
    {
        return (new TemplatedEmail())->from($this->senderEmail);
    }

    private function createContext(array $context, ?Organization $organization = null): array
    {
        if (!$organization) {
            return $context;
        }

        return array_merge($context, array_filter([
            'platform_name' => $organization->getWhiteLabelName(),
            'platform_logo_url' => $organization->getWhiteLabelLogo() ? $this->cdnRouter->generateUrl($organization->getWhiteLabelLogo()) : null,
            'platform_email' => $organization->getBillingEmail(),
        ], static fn ($value) => null !== $value && '' !== $value));
    }
}
