<?php

namespace App\Community\Webhook;

use App\Api\Model\ContactApiData;
use App\Api\Persister\ContactApiPersister;
use App\Repository\ProjectRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WingsWebhookHandler
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private ContactApiPersister $contactPersister,
    ) {
    }

    public function __invoke(WingsWebhookMessage $message)
    {
        if (!$project = $this->projectRepository->find($message->getProjectId())) {
            return true;
        }

        $payload = $message->getPayload();

        // Create API payload
        $apiData = match ($payload['action'] ?? null) {
            'signature.created', 'signature.confirmed' => $this->createApiDataForSignature($payload),
            'submission.created', 'submission.confirmed' => $this->createApiDataForSubmission($payload),
            'donation.created', 'donation.confirmed' => $this->createApiDataForDonation($payload),
            'attendance.created', 'attendance.confirmed' => $this->createApiDataForAttendance($payload),
            'signup.created' => $this->createApiDataForSignup($payload),
            default => null,
        };

        // Event not supported
        if (!$apiData) {
            return true;
        }

        // Persist contact using API persister to trigger integrations and internal updates
        $this->contactPersister->persist($apiData, $project->getOrganization());

        return true;
    }

    private function createApiDataForSignature(array $payload): ContactApiData
    {
        return ContactApiData::createFromPayload([
            'email' => $payload['signature']['constituent']['email'],
            'profileFirstName' => $payload['signature']['constituent']['firstName'] ?? null,
            'profileLastName' => $payload['signature']['constituent']['lastName'] ?? null,
            'settingsReceiveNewsletters' => $payload['signature']['newsletter'] ?? false,
            'metadataTags' => array_filter([$payload['signature']['petition']['slug'] ?? null]),
            'metadataSource' => 'wings',
        ]);
    }

    private function createApiDataForSubmission(array $payload): ContactApiData
    {
        return ContactApiData::createFromPayload([
            'email' => $payload['submission']['constituent']['email'],
            'profileFirstName' => $payload['submission']['constituent']['firstName'] ?? null,
            'profileLastName' => $payload['submission']['constituent']['lastName'] ?? null,
            'settingsReceiveNewsletters' => $payload['submission']['newsletter'] ?? false,
            'metadataTags' => array_filter([$payload['submission']['signup']['slug'] ?? null]),
            'metadataSource' => 'wings',
        ]);
    }

    private function createApiDataForDonation(array $payload): ContactApiData
    {
        return ContactApiData::createFromPayload([
            'email' => $payload['donation']['constituent']['email'],
            'profileFirstName' => $payload['donation']['constituent']['firstName'] ?? null,
            'profileLastName' => $payload['donation']['constituent']['lastName'] ?? null,
            'settingsReceiveNewsletters' => $payload['donation']['newsletter'] ?? false,
            'metadataTags' => array_filter([$payload['donation']['donation']['slug'] ?? null]),
            'metadataSource' => 'wings',
        ]);
    }

    private function createApiDataForAttendance(array $payload): ContactApiData
    {
        return ContactApiData::createFromPayload([
            'email' => $payload['attendance']['constituent']['email'],
            'profileFirstName' => $payload['attendance']['constituent']['firstName'] ?? null,
            'profileLastName' => $payload['attendance']['constituent']['lastName'] ?? null,
            'settingsReceiveNewsletters' => $payload['attendance']['newsletter'] ?? false,
            'metadataTags' => array_filter([$payload['attendance']['attendance']['slug'] ?? null]),
            'metadataSource' => 'wings',
        ]);
    }

    private function createApiDataForSignup(array $payload): ContactApiData
    {
        return ContactApiData::createFromPayload([
            'email' => $payload['signup']['constituent']['email'],
            'profileFirstName' => $payload['signup']['constituent']['firstName'] ?? null,
            'profileLastName' => $payload['signup']['constituent']['lastName'] ?? null,
            'settingsReceiveNewsletters' => $payload['signup']['newsletter'] ?? false,
            'metadataTags' => array_filter([$payload['signup']['signup']['slug'] ?? null]),
            'metadataSource' => 'wings',
        ]);
    }
}
