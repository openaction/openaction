<?php

namespace App\Bridge\Postmark;

use App\Bridge\Postmark\Model\Mail;
use App\Entity\Model\PostmarkDomainConfig;
use Postmark\PostmarkAdminClient;
use Postmark\PostmarkClient;

class Postmark implements PostmarkInterface
{
    private PostmarkAdminClient $client;
    private PostmarkClient $sendClient;

    public function __construct(string $accountToken, string $serverToken)
    {
        $this->client = new PostmarkAdminClient($accountToken);
        $this->sendClient = new PostmarkClient($serverToken);
    }

    public function authenticateRootDomain(string $domain): PostmarkDomainConfig
    {
        return $this->mapToConfig($this->client->createDomain($domain, 'pm-bounces.'.$domain));
    }

    public function getRootDomainConfig(string $domainId): PostmarkDomainConfig
    {
        return $this->mapToConfig($this->client->getDomain($domainId));
    }

    public function sendMessage(Mail $mail): void
    {
        $emails = [];
        foreach ($mail->personalizations as $personalization) {
            $email = [
                'From' => $mail->fromName ? sprintf('%s <%s>', $mail->fromName, $mail->fromEmail) : $mail->fromEmail,
                'To' => $personalization->to,
                'Subject' => $mail->subject,
                'HtmlBody' => $personalization->htmlContent,
                'TrackOpens' => $mail->trackOpens,
                'TrackLinks' => $mail->trackClicks ? 'HtmlAndText' : 'None',
                'Metadata' => $personalization->metadata,
                'MessageStream' => 'marketing',
            ];

            if ($mail->replyToEmail) {
                $email['ReplyTo'] = $mail->replyToName ? sprintf('%s <%s>', $mail->replyToName, $mail->replyToEmail) : $mail->replyToEmail;
            }

            $emails[] = $email;
        }

        foreach (array_chunk($emails, 250) as $chunk) {
            $this->sendClient->sendEmailBatch($chunk);
        }
    }

    private function mapToConfig(object $data): PostmarkDomainConfig
    {
        return new PostmarkDomainConfig(
            $data->id,
            $data->name,
            $data->dkimverified,
            $data->dkimhost ?: $data->dkimpendinghost ?: $data->dkimrevokedhost,
            $data->dkimtextvalue ?: $data->dkimpendingtextvalue ?: $data->dkimrevokedtextvalue,
            $data->returnpathdomainverified,
            $data->returnpathdomain,
            $data->returnpathdomaincnamevalue,
        );
    }
}
