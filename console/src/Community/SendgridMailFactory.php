<?php

namespace App\Community;

use App\Bridge\Sendgrid\Model\Recipient;
use App\Entity\Community\EmailAutomation;
use App\Entity\Community\EmailingCampaign;
use SendGrid\Mail\CustomArg;
use SendGrid\Mail\From;
use SendGrid\Mail\Header;
use SendGrid\Mail\Mail;
use SendGrid\Mail\To;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

use function Symfony\Component\String\u;

class SendgridMailFactory
{
    public function __construct(
        private readonly Environment $twig,
        private readonly UrlGeneratorInterface $router,
    ) {
    }

    /**
     * @param Recipient[] $recipients
     *
     * @throws \SendGrid\Mail\TypeException
     */
    public function createEmailing(EmailingCampaign $campaign, array $recipients, bool $preview = false): Mail
    {
        $fromEmail = u($campaign->getFullFromEmail())->ascii()->toString();

        $mail = new Mail(new From($fromEmail, $campaign->getFromName() ?: null));
        $mail->setGlobalSubject(($preview ? 'Preview - ' : '').$campaign->getSubject());
        $mail->setSubject(($preview ? 'Preview - ' : '').$campaign->getSubject());
        $mail->setOpenTracking($campaign->hasTrackOpens());
        $mail->setClickTracking($campaign->hasTrackClicks());
        $mail->setFooter(false);
        $mail->addContent('text/html', $this->createCampaignBody($campaign, $preview));

        if ($campaign->getReplyToEmail()) {
            $mail->setReplyTo($campaign->getReplyToEmail(), $campaign->getReplyToName());
        }

        foreach ($this->cleanRecipients($recipients) as $email => $recipient) {
            $personalization = $mail->getPersonalization($mail->getPersonalizationCount());
            $personalization->addTo(new To($email));
            $personalization->addCustomArg(new CustomArg('message-uuid', $recipient->getMessageId()));
            $personalization->addHeader(new Header('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click'));
            $personalization->addHeader(new Header('List-Unsubscribe', sprintf(
                '<%s>',
                $this->router->generate('webhook_list_unsubscribe', ['contactUuid' => $recipient->getMessageId()]),
            )));

            foreach ($recipient->getVariables() as $name => $substitute) {
                $personalization->addSubstitution($name, $substitute);
            }
        }

        return $mail;
    }

    /**
     * @throws \SendGrid\Mail\TypeException
     */
    public function createAutomationEmail(EmailAutomation $automation, Recipient $recipient): Mail
    {
        $mail = new Mail(new From($automation->getFromEmail(), $automation->getFromName() ?: null));
        $mail->setSubject($automation->getSubject());
        $mail->setOpenTracking($automation->getOrganization()->getEmailEnableOpenTracking());
        $mail->setClickTracking($automation->getOrganization()->getEmailEnableClickTracking());
        $mail->setFooter(false);
        $mail->addContent('text/html', $this->createAutomationBody($automation));

        if ($automation->getReplyToEmail()) {
            $mail->setReplyTo($automation->getReplyToEmail(), $automation->getReplyToName());
        }

        $personalization = $mail->getPersonalization($mail->getPersonalizationCount());
        $personalization->addTo(new To($automation->getToEmail() ?: $recipient->getEmail()));

        foreach ($recipient->getVariables() as $name => $substitute) {
            $personalization->addSubstitution($name, $substitute);
        }

        return $mail;
    }

    public function createCampaignBody(EmailingCampaign $campaign, bool $preview = false): string
    {
        return $this->twig->render('emails/community/emailing_campaign.html.twig', [
            'campaign' => $campaign,
            'project' => $campaign->getProject(),
            'organization' => $campaign->getProject()->getOrganization(),
            'preview' => $preview,
        ]);
    }

    public function createMailchimpCampaignBody(EmailingCampaign $campaign, bool $preview = false): string
    {
        return $this->twig->render('emails/community/mailchimp_emailing_campaign.html.twig', [
            'campaign' => $campaign,
            'project' => $campaign->getProject(),
            'organization' => $campaign->getProject()->getOrganization(),
            'preview' => $preview,
        ]);
    }

    public function createBrevoCampaignBody(EmailingCampaign $campaign, bool $preview = false): string
    {
        return $this->createMailchimpCampaignBody($campaign, $preview);
    }

    public function createAutomationBody(EmailAutomation $automation, bool $preview = false): string
    {
        return $this->twig->render('emails/community/email_automation.html.twig', [
            'automation' => $automation,
            'organization' => $automation->getOrganization(),
            'preview' => $preview,
        ]);
    }

    /**
     * @param Recipient[] $recipients
     *
     * @return Recipient[]
     */
    private function cleanRecipients(array $recipients): array
    {
        $cleaned = [];
        foreach ($recipients as $recipient) {
            $cleaned[$recipient->getEmail()] = $recipient;
        }

        return $cleaned;
    }
}
