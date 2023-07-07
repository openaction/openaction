<?php

namespace App\Mailer;

use App\Entity\Billing\Order;
use App\Entity\Billing\Quote;
use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingOrder;
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
    private string $senderEmail;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, string $senderEmail)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->senderEmail = $senderEmail;
    }

    public function sendRegistrationVerify(Registration $registration)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($registration->getEmail())
                ->subject($this->translator->trans('transactional.registration.verify.subject', [], 'emails', $registration->getLocale()))
                ->htmlTemplate('emails/security/registration/verify.html.twig')
                ->context([
                    'locale' => $registration->getLocale(),
                    'registration_uuid' => $registration->getUuid()->toRfc4122(),
                    'registration_token' => $registration->getToken(),
                ])
        );
    }

    public function sendRegistrationWelcome(User $user)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans('transactional.registration.welcome.subject', [], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/security/registration/welcome.html.twig')
                ->context([
                    'locale' => $user->getLocale(),
                    'name' => $user->getFullName(),
                ])
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
                ->context([
                    'locale' => $registration->getLocale(),
                    'registration_uuid' => $registration->getUuid()->toRfc4122(),
                    'registration_token' => $registration->getToken(),
                    'organization_name' => $organization->getName(),
                    'author_name' => $author->getFullName(),
                ])
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
                ->context([
                    'locale' => $invited->getLocale(),
                    'organization_uuid' => $organization->getUuid()->toRfc4122(),
                    'organization_name' => $organization->getName(),
                    'author_name' => $author->getFullName(),
                ])
        );
    }

    public function sendForgottenPasswordRequest(User $user)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans('transactional.forgot_password.request.subject', [], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/security/forgot-password/request.html.twig')
                ->context([
                    'locale' => $user->getLocale(),
                    'secret' => $user->getSecretResetPassword(),
                ])
        );
    }

    public function sendPasswordUpdated(User $user)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans('transactional.forgot_password.updated.subject', [], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/security/forgot-password/updated.html.twig')
                ->context([
                    'locale' => $user->getLocale(),
                ])
        );
    }

    public function sendNotificationNewProject(User $user, Organization $organization, Project $project)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($user->getEmail())
                ->subject($this->translator->trans('transactional.notification.new_project.subject', [], 'emails', $user->getLocale()))
                ->htmlTemplate('emails/console/notification/new_project.html.twig')
                ->context([
                    'locale' => $user->getLocale(),
                    'project_name' => $project->getName(),
                    'organization_name' => $organization->getName(),
                ])
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
                ->context([
                    'locale' => $user->getLocale(),
                    'organization_name' => $organization->getName(),
                    'days_left' => $daysLeft,
                ])
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
                ->context([
                    'organization_name' => $order->getOrganization()->getName(),
                    'locale' => $order->getRecipient()->getLocale(),
                ])
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
                ->context([
                    'organization_name' => $quote->getOrganization()->getName(),
                    'locale' => $quote->getRecipient()->getLocale(),
                ])
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
                ->context([
                    'organization_uuid' => $orga->getUuid(),
                    'organization_name' => $orga->getName(),
                    'locale' => $locale,
                    'export_pathname' => $upload->getPathname(),
                ])
        );
    }

    public function sendNotificationPrintFinalized(PrintingOrder $order)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($order->getRecipientEmail())
                ->subject('[Citipo] Nous avons bien reçu votre commande n°'.$order->getReference())
                ->htmlTemplate('emails/console/notification/print_finalized.html.twig')
                ->context([
                    'reference' => $order->getReference(),
                    'candidate' => $order->getRecipientCandidate(),
                    'project_uuid' => $order->getProject()->getUuid()->toRfc4122(),
                    'organization_uuid' => $order->getProject()->getOrganization()->getUuid()->toRfc4122(),
                    'order_uuid' => $order->getOrder()->getUuid()->toRfc4122(),
                    'locale' => 'fr',
                ])
        );
    }

    public function sendNotificationPrintSubrogatedFinalized(PrintingOrder $order)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($order->getRecipientEmail())
                ->subject('[Citipo] Nous avons bien reçu votre commande n°'.$order->getReference())
                ->htmlTemplate('emails/console/notification/print_subrogated.html.twig')
                ->context([
                    'reference' => $order->getReference(),
                    'candidate' => $order->getRecipientCandidate(),
                    'project_uuid' => $order->getProject()->getUuid()->toRfc4122(),
                    'organization_uuid' => $order->getProject()->getOrganization()->getUuid()->toRfc4122(),
                    'order_uuid' => $order->getOrder()->getUuid()->toRfc4122(),
                    'locale' => 'fr',
                ])
        );
    }

    public function sendNotificationPrintBatReceived(PrintingCampaign $campaign)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($campaign->getPrintingOrder()->getRecipientEmail())
                ->subject('[Citipo] Un Bon À Tirer est prêt à être examiné pour la commande n°'.$campaign->getPrintingOrder()->getReference())
                ->htmlTemplate('emails/console/notification/print_bat.html.twig')
                ->context([
                    'reference' => $campaign->getPrintingOrder()->getReference(),
                    'candidate' => $campaign->getPrintingOrder()->getRecipientCandidate(),
                    'project_uuid' => $campaign->getPrintingOrder()->getProject()->getUuid()->toRfc4122(),
                    'organization_uuid' => $campaign->getPrintingOrder()->getProject()->getOrganization()->getUuid()->toRfc4122(),
                    'campaign_uuid' => $campaign->getUuid()->toRfc4122(),
                    'locale' => 'fr',
                ])
        );
    }

    public function sendNotificationPrintProductionStarted(PrintingOrder $order)
    {
        $this->mailer->send(
            $this->createMessage()
                ->to($order->getRecipientEmail())
                ->subject('[Citipo] Votre commande n°'.$order->getReference().' vient d\'être envoyée en production')
                ->htmlTemplate('emails/console/notification/print_production_started.html.twig')
                ->context([
                    'reference' => $order->getReference(),
                    'candidate' => $order->getRecipientCandidate(),
                    'project_uuid' => $order->getProject()->getUuid()->toRfc4122(),
                    'organization_uuid' => $order->getProject()->getOrganization()->getUuid()->toRfc4122(),
                    'locale' => 'fr',
                ])
        );
    }

    private function createMessage(): TemplatedEmail
    {
        return (new TemplatedEmail())->from($this->senderEmail);
    }
}
