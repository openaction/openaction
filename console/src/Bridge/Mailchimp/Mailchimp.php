<?php

namespace App\Bridge\Mailchimp;

use App\Entity\Community\EmailingCampaign;
use App\Entity\Organization;
use GuzzleHttp\Exception\ClientException;
use MailchimpMarketing\ApiClient;
use Psr\Log\LoggerInterface;

class Mailchimp implements MailchimpInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function sendCampaign(EmailingCampaign $campaign, string $htmlContent, array $contacts): string
    {
        try {
            $client = $this->createClient($campaign->getProject()->getOrganization());

            // Target tag name
            $campaignTag = 'citipo-campaign-'.$campaign->getId();

            // Resolve audience ID
            $citipoListName = $campaign->getProject()->getOrganization()->getMailchimpAudienceName();
            $citipoListId = null;

            foreach ($client->lists->getAllLists(count: 1000)->lists ?? [] as $list) {
                if ($citipoListName === $list->name) {
                    $citipoListId = $list->id;
                    break;
                }
            }

            if (!$citipoListId) {
                throw new \InvalidArgumentException('Invalid Mailchimp audience name: "'.$citipoListName.'".');
            }

            // Sync campaign contacts
            foreach (array_chunk($contacts, 500) as $chunk) {
                $members = [];
                foreach ($chunk as $contact) {
                    $members[] = [
                        'email_address' => $contact['email'],
                        'status' => 'subscribed',
                        'timestamp_signup' => $contact['createdAt']->format(\DateTime::ATOM),
                        'timestamp_opt' => $contact['createdAt']->format(\DateTime::ATOM),
                        'tags' => [$campaignTag],
                    ];
                }

                $client->lists->batchListMembers($citipoListId, [
                    'members' => $members,
                    'update_existing' => true,
                ]);
            }

            // Find tag ID
            $campaignTagId = null;
            foreach ($client->lists->listSegments($citipoListId)->segments ?? [] as $tag) {
                if ($tag->name === $campaignTag) {
                    $campaignTagId = $tag->id;
                }
            }

            // Create campaign
            $mailchimpCampaign = $client->campaigns->create([
                'type' => 'regular',
                'recipients' => [
                    'list_id' => $citipoListId,
                    'segment_opts' => [
                        'saved_segment_id' => $campaignTagId,
                    ],
                ],
                'settings' => [
                    'subject_line' => $campaign->getSubject(),
                    'preview_text' => $campaign->getPreview() ?: '',
                    'title' => $campaign->getSubject(),
                    'from_name' => $campaign->getFromName(),
                    'reply_to' => $campaign->getReplyToEmail() ?: $campaign->getFullFromEmail(),
                ],
                'tracking' => [
                    'opens' => true,
                    'html_clicks' => true,
                    'text_clicks' => true,
                ],
            ]);

            $client->campaigns->setContent($mailchimpCampaign->id, [
                'html' => $htmlContent,
            ]);

            $client->campaigns->send($mailchimpCampaign->id);

            return $mailchimpCampaign->id;
        } catch (ClientException $exception) {
            $this->logger->error('ClientException: '.$exception->getMessage(), [
                'exception' => $exception,
                'request_url' => $exception->getRequest()->getMethod().' '.$exception->getRequest()->getUri(),
                'request_body' => (string) $exception->getRequest()->getBody(),
                'response_code' => $exception->getResponse()->getStatusCode(),
                'response_body' => (string) $exception->getResponse()->getBody(),
            ]);

            throw new \RuntimeException('Mailchimp error: '.$exception->getResponse()->getBody());
        }
    }

    private function createClient(Organization $organization): ApiClient
    {
        $client = new ApiClient();
        $client->setConfig([
            'apiKey' => $organization->getMailchimpApiKey(),
            'server' => $organization->getMailchimpServerPrefix(),
        ]);

        return $client;
    }
}
