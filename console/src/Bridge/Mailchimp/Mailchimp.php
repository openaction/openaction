<?php

namespace App\Bridge\Mailchimp;

use App\Entity\Community\EmailingCampaign;
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
        $orga = $campaign->getProject()->getOrganization();
        $client = $this->createClient($orga->getMailchimpApiKey(), $orga->getMailchimpServerPrefix());

        try {
            // Target tag name
            $campaignTag = 'citipo-campaign-'.$campaign->getId();

            // Resolve audience ID
            $citipoListName = $campaign->getProject()->getOrganization()->getMailchimpAudienceName();
            $citipoListId = null;

            $offset = 0;
            $mailchimpLists = [];
            while ($lists = $client->lists->getAllLists(count: 1000, offset: $offset)->lists ?? []) {
                $offset += 1000;
                $mailchimpLists = array_merge($mailchimpLists, $lists);
            }

            foreach ($mailchimpLists as $list) {
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

            $offset = 0;
            $mailchimpSegments = [];
            while ($segments = $client->lists->listSegments($citipoListId, count: 1000, offset: $offset)->segments ?? []) {
                $offset += 1000;
                $mailchimpSegments = array_merge($mailchimpSegments, $segments);
            }

            foreach ($mailchimpSegments as $tag) {
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
                    'opens' => $campaign->hasTrackOpens(),
                    'html_clicks' => $campaign->hasTrackClicks(),
                    'text_clicks' => $campaign->hasTrackClicks(),
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

    public function getCampaignReport(string $apiKey, string $serverPrefix, string $campaignId): array
    {
        $client = $this->createClient($apiKey, $serverPrefix);

        try {
            $report = [];

            $offset = 0;
            while ($activities = $client->reports->getEmailActivityForCampaign($campaignId, count: 1000, offset: $offset)->emails ?? []) {
                $offset += 1000;

                foreach ($activities as $activity) {
                    $report[$activity->email_address] = [
                        'opens' => 0,
                        'clicks' => 0,
                    ];

                    foreach ($activity->activity ?? [] as $action) {
                        if ('open' === $action->action) {
                            ++$report[$activity->email_address]['opens'];
                        } elseif ('click' === $action->action) {
                            ++$report[$activity->email_address]['clicks'];
                        }
                    }
                }
            }

            return $report;
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

    private function createClient(string $apiKey, string $serverPrefix): ApiClient
    {
        $client = new ApiClient();
        $client->setConfig(['apiKey' => $apiKey, 'server' => $serverPrefix]);

        return $client;
    }
}
