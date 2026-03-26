<?php

namespace App\Community;

use App\Bridge\Brevo\BrevoInterface;
use App\Bridge\Sendgrid\Consumer\SendgridMessage;
use App\Bridge\Sendgrid\Model\Recipient;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Organization;
use App\Repository\Community\EmailAutomationMessageRepository;
use App\Repository\OrganizationRepository;
use App\Util\Uid;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class EmailAutomationSender
{
    private const UNRESOLVED_TOKEN_PATTERN = '/-[a-z][a-z0-9]*(?:-[a-z0-9]+)*-/i';

    public function __construct(
        private EntityManagerInterface $em,
        private OrganizationRepository $organizationRepository,
        private SendgridMailFactory $messageFactory,
        private BrevoInterface $brevo,
        private EmailAutomationMessageRepository $messageRepository,
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
    ) {
    }

    public function send(EmailAutomation $automation, ?Contact $contact, array $additionalVariables = []): bool
    {
        if (!$this->organizationRepository->useCredits($automation->getOrganization(), 1, 'automation')) {
            return false;
        }

        // If no automation email and no contact email, ignore
        if (!$automation->getToEmail() && (!$contact || !$contact->getEmail())) {
            return false;
        }

        // Persist the automation message
        if ($automation->getToEmail()) {
            $this->messageRepository->createNotificationMessage($automation);
        } elseif ($contact && $contact->getEmail()) {
            $this->messageRepository->createMessage($automation, $contact);
        }

        // Send the automation
        $recipient = null;
        if ($automation->getToEmail()) {
            $recipient = Recipient::createFromNotification($automation->getToEmail(), Uid::random(), $additionalVariables);
        } elseif ($contact) {
            $recipient = Recipient::createFromContact($contact, Uid::random(), $additionalVariables);
        }

        if ($recipient) {
            $organization = $automation->getOrganization();
            if ($this->isBrevoConfigured($organization)) {
                $customVariables = $recipient->getVariables();
                if ($additionalVariables) {
                    $customVariables = array_merge($customVariables, $additionalVariables);
                }

                $htmlContent = $this->renderHtmlContent(
                    $this->messageFactory->createAutomationBody($automation),
                    $this->normalizeSubstitutions($customVariables),
                );
                $unresolvedTokens = $this->extractUnresolvedTokens($htmlContent);
                if ($unresolvedTokens) {
                    $this->logger->warning('Brevo transactional email contains unresolved merge tokens after rendering', [
                        'automation_id' => $automation->getId(),
                        'automation_uuid' => $automation->getUuid()->toRfc4122(),
                        'to_email' => $recipient->getEmail(),
                        'tokens' => $unresolvedTokens,
                    ]);
                }

                $this->brevo->sendTransactionalEmail(
                    apiKey: (string) $organization->getBrevoApiKey(),
                    fromEmail: (string) $organization->getBrevoSenderEmail(),
                    fromName: $organization->getName(),
                    toEmail: $recipient->getEmail(),
                    subject: $automation->getSubject(),
                    htmlContent: $htmlContent,
                    replyToEmail: $automation->getReplyToEmail(),
                    replyToName: $automation->getReplyToName(),
                    customVariables: $customVariables,
                );

                return true;
            }

            $batch = $this->messageFactory->createAutomationBatch($automation, $recipient);
            $this->em->persist($batch);
            $this->em->flush();

            $this->bus->dispatch(new SendgridMessage($batch->getId()));
        }

        return true;
    }

    private function isBrevoConfigured(Organization $organization): bool
    {
        return 'brevo' === $organization->getEmailProvider()
            && (bool) $organization->getBrevoApiKey()
            && (bool) $organization->getBrevoSenderEmail();
    }

    /**
     * @param array<string, mixed> $substitutions
     *
     * @return array<string, string>
     */
    private function normalizeSubstitutions(array $substitutions): array
    {
        $normalized = [];

        foreach ($substitutions as $key => $value) {
            if (!\is_string($key)) {
                continue;
            }

            $key = trim($key);
            if ('' === $key) {
                continue;
            }

            $normalized[$key] = $this->normalizeSubstitutionValue($value);
        }

        return $normalized;
    }

    private function normalizeSubstitutionValue(mixed $value): string
    {
        if (null === $value) {
            return '';
        }

        if (\is_scalar($value) || $value instanceof \Stringable) {
            return (string) $value;
        }

        try {
            return (string) json_encode($value, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return '';
        }
    }

    /**
     * @param array<string, string> $substitutions
     */
    private function renderHtmlContent(string $content, array $substitutions): string
    {
        if (!$substitutions) {
            return $content;
        }

        return strtr($content, $substitutions);
    }

    /**
     * @return string[]
     */
    private function extractUnresolvedTokens(string $content): array
    {
        if (!preg_match_all(self::UNRESOLVED_TOKEN_PATTERN, $content, $matches)) {
            return [];
        }

        return array_values(array_unique($matches[0]));
    }
}
