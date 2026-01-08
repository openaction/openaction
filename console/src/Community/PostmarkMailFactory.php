<?php

namespace App\Community;

use App\Bridge\Postmark\Model\Mail;
use App\Bridge\Postmark\Model\Personalization;
use App\Bridge\Sendgrid\Model\Recipient;
use App\Entity\Community\EmailingCampaign;
use App\Entity\Community\EmailingCampaignBatch;
use Twig\Environment;

use function Symfony\Component\String\u;

class PostmarkMailFactory
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param Recipient[] $recipients
     */
    public function createBatch(EmailingCampaign $campaign, array $recipients, bool $preview = false): EmailingCampaignBatch
    {
        $htmlContent = $this->createCampaignBody($campaign, $preview);

        $personnalizations = [];
        foreach ($this->cleanRecipients($recipients) as $email => $recipient) {
            $personnalizations[] = [
                'to' => $email,
                'metadata' => ['Message-Uuid' => $recipient->getMessageId()],
                'htmlContent' => str_replace(
                    array_keys($recipient->getVariables()),
                    array_values($recipient->getVariables()),
                    $htmlContent,
                ),
            ];
        }

        return new EmailingCampaignBatch($campaign, 'postmark', [
            'fromEmail' => u($campaign->getFullFromEmail())->ascii()->toString(),
            'fromName' => $campaign->getFromName() ?: null,
            'replyToEmail' => $campaign->getReplyToEmail() ?: null,
            'replyToName' => $campaign->getReplyToName() ?: null,
            'subject' => ($preview ? 'Preview - ' : '').$campaign->getSubject(),
            'trackOpens' => $campaign->hasTrackOpens(),
            'trackClicks' => $campaign->hasTrackClicks(),
            'personalizations' => $personnalizations,
        ]);
    }

    public function createMailFromBatch(EmailingCampaignBatch $batch): Mail
    {
        $personnalizations = [];
        foreach ($batch->getPayload()['personalizations'] as $data) {
            $personnalizations[] = new Personalization($data['to'], $data['metadata'], $data['htmlContent']);
        }

        return new Mail(
            fromEmail: $batch->getPayload()['fromEmail'],
            fromName: $batch->getPayload()['fromName'],
            replyToEmail: $batch->getPayload()['replyToEmail'],
            replyToName: $batch->getPayload()['replyToName'],
            subject: $batch->getPayload()['subject'],
            trackOpens: $batch->getPayload()['trackOpens'],
            trackClicks: $batch->getPayload()['trackClicks'],
            personalizations: $personnalizations,
        );
    }

    private function createCampaignBody(EmailingCampaign $campaign, bool $preview = false): string
    {
        return $this->twig->render('emails/community/emailing_campaign.html.twig', [
            'campaign' => $campaign,
            'project' => $campaign->getProject(),
            'organization' => $campaign->getProject()?->getOrganization(),
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
