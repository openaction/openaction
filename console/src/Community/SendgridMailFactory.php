<?php

namespace App\Community;

use App\Bridge\Sendgrid\Model\Recipient;
use App\Entity\Community\EmailAutomation;
use App\Entity\Community\EmailBatch;
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
     */
    public function createCampaignBatch(EmailingCampaign $campaign, array $recipients, bool $preview = false): EmailBatch
    {
        $personnalizations = [];
        foreach ($this->cleanRecipients($recipients) as $email => $recipient) {
            $personnalizations[] = [
                'to' => $email,
                'message-uuid' => $recipient->getMessageId(),
                'list-unsubscribe' => sprintf(
                    '<%s>',
                    $this->router->generate('webhook_list_unsubscribe', ['contactUuid' => $recipient->getMessageId()]),
                ),
                'webhook-url' => $this->router->generate('webhook_sendgrid'),
                'substitutions' => $recipient->getVariables(),
            ];
        }

        return new EmailBatch('campaign:'.$campaign->getId(), 'sendgrid', [
            'fromEmail' => u($campaign->getFullFromEmail())->ascii()->toString(),
            'fromName' => $campaign->getFromName() ?: null,
            'subject' => ($preview ? 'Preview - ' : '').$campaign->getSubject(),
            'replyToEmail' => $campaign->getReplyToEmail() ?: null,
            'replyToName' => $campaign->getReplyToName() ?: null,
            'trackOpens' => $campaign->hasTrackOpens(),
            'trackClicks' => $campaign->hasTrackClicks(),
            'content' => $this->createCampaignBody($campaign, $preview),
            'personalizations' => $personnalizations,
        ]);
    }

    /**
     * @throws \SendGrid\Mail\TypeException
     */
    public function createAutomationBatch(EmailAutomation $automation, Recipient $recipient): EmailBatch
    {
        return new EmailBatch('automation:'.$automation->getId(), 'sendgrid', [
            'fromEmail' => u($automation->getFromEmail())->ascii()->toString(),
            'fromName' => $automation->getFromName() ?: null,
            'subject' => $automation->getSubject(),
            'replyToEmail' => $automation->getReplyToEmail() ?: null,
            'replyToName' => $automation->getReplyToName() ?: null,
            'trackOpens' => $automation->getOrganization()?->getEmailEnableOpenTracking(),
            'trackClicks' => $automation->getOrganization()?->getEmailEnableClickTracking(),
            'content' => $this->createAutomationBody($automation),
            'personalizations' => [
                [
                    'to' => $automation->getToEmail() ?: $recipient->getEmail(),
                    'message-uuid' => $recipient->getMessageId(),
                    'substitutions' => $recipient->getVariables(),
                ],
            ],
        ]);
    }

    public function createMailFromBatch(EmailBatch $batch): Mail
    {
        $mail = new Mail(new From($batch->getPayload()['fromEmail'], $batch->getPayload()['fromName']));
        $mail->setGlobalSubject($batch->getPayload()['subject']);
        $mail->setSubject($batch->getPayload()['subject']);
        $mail->setOpenTracking($batch->getPayload()['trackOpens']);
        $mail->setClickTracking($batch->getPayload()['trackClicks']);
        $mail->setFooter(false);
        $mail->addContent('text/html', $batch->getPayload()['content']);

        if ($batch->getPayload()['replyToEmail']) {
            $mail->setReplyTo($batch->getPayload()['replyToEmail'], $batch->getPayload()['replyToName']);
        }

        foreach ($batch->getPayload()['personalizations'] as $data) {
            $personalization = $mail->getPersonalization($mail->getPersonalizationCount());
            $personalization->addTo(new To($data['to']));

            if (isset($data['message-uuid'])) {
                $personalization->addCustomArg(new CustomArg('message-uuid', $data['message-uuid']));
            }

            if (isset($data['list-unsubscribe'])) {
                $personalization->addHeader(new Header('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click'));
                $personalization->addHeader(new Header('List-Unsubscribe', $data['list-unsubscribe']));
            }

            foreach ($data['substitutions'] as $name => $substitute) {
                $personalization->addSubstitution($name, $substitute);
            }
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
