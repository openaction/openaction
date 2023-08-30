<?php

namespace App\Bridge\Quorum;

use App\Bridge\Quorum\Consumer\QuorumMessage;
use App\Entity\Community\Contact;
use App\Repository\Platform\LogRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class Quorum implements QuorumInterface
{
    public function __construct(private LogRepository $logRepository, private MessageBusInterface $bus)
    {
    }

    public function persist(Contact $contact)
    {
        if (!$orga = $contact->getOrganization()) {
            return;
        }

        if (!$token = $orga->getQuorumToken()) {
            return;
        }

        if (!$city = $contact->getAddressCity() ?: $orga->getQuorumDefaultCity()) {
            return;
        }

        $tags = [];
        foreach ($contact->getMetadataTags() as $tag) {
            $tags[] = $tag->getName();
        }

        $payload = [
            'person' => [
                'id' => $contact->getId(),
                'first_name' => $contact->getProfileFirstName(),
                'last_name' => $contact->getProfileLastName(),
                'email' => $contact->getEmail(),
                'email_opt_in' => $contact->hasSettingsReceiveNewsletters(),
                'mobile' => $contact->getContactPhone(),
                'mobile_opt_in' => $contact->hasSettingsReceiveSms(),
                'birthdate' => $contact->getProfileBirthdate() ? $contact->getProfileBirthdate()->format('Y-m-d') : null,
                'home_address' => [
                    'city' => strtolower($city),
                    'zip' => $contact->getAddressZipCode(),
                    'country_code' => $contact->getAddressCountry() ? $contact->getAddressCountry()->getCode() : null,
                ],
                'tags' => $tags,
            ],
        ];

        if ($number = $contact->getAddressStreetNumber()) {
            $payload['person']['home_address']['street_number'] = $number;
        }

        if ($address = $contact->getAddressStreetLine1()) {
            $payload['person']['home_address']['street_name'] = $address;
        }

        // Log payload for debugging
        $this->logRepository->createLog('quorum_message', ['token' => $token, 'payload' => $payload]);

        $this->bus->dispatch(new QuorumMessage(['token' => $token, 'payload' => $payload]));
    }
}
