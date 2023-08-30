<?php

namespace App\Bridge\Spallian;

use App\Bridge\Spallian\Consumer\SpallianMessage;
use App\Entity\Community\Contact;
use App\Util\Uid;
use Symfony\Component\Messenger\MessageBusInterface;

class Spallian implements SpallianInterface
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function persist(Contact $contact, bool $isCreation)
    {
        if (!$orga = $contact->getOrganization()) {
            return;
        }

        if (!$endpoint = $orga->getSpallianEndpoint()) {
            return;
        }

        $tags = [];
        foreach ($contact->getMetadataTags() as $tag) {
            $tags[] = $tag->getName();
        }

        $this->bus->dispatch(new SpallianMessage($endpoint.'?'.($isCreation ? 'create' : 'update').'=1', [
            'id' => Uid::toBase62($contact->getUuid()),
            'email' => $contact->getEmail(),
            'profileFormalTitle' => $contact->getProfileFormalTitle(),
            'profileFirstName' => $contact->getProfileFirstName(),
            'profileMiddleName' => $contact->getProfileMiddleName(),
            'profileLastName' => $contact->getProfileLastName(),
            'profileBirthdate' => $contact->getProfileBirthdate(),
            'profileGender' => $contact->getProfileGender(),
            'profileCompany' => $contact->getProfileCompany(),
            'profileJobTitle' => $contact->getProfileJobTitle(),
            'accountLanguage' => $contact->getAccountLanguage(),
            'contactAdditionalEmails' => $contact->getContactAdditionalEmails(),
            'contactPhone' => $contact->getContactPhone(),
            'contactWorkPhone' => $contact->getContactWorkPhone(),
            'socialFacebook' => $contact->getSocialFacebook(),
            'socialTwitter' => $contact->getSocialTwitter(),
            'socialLinkedIn' => $contact->getSocialLinkedIn(),
            'socialTelegram' => $contact->getSocialTelegram(),
            'socialWhatsapp' => $contact->getSocialWhatsapp(),
            'addressStreetLine1' => $contact->getAddressStreetLine1(),
            'addressStreetLine2' => $contact->getAddressStreetLine2(),
            'addressZipCode' => $contact->getAddressZipCode(),
            'addressCity' => $contact->getAddressCity(),
            'addressCountry' => $contact->getAddressCountry(),
            'settingsReceiveNewsletters' => $contact->hasSettingsReceiveNewsletters(),
            'settingsReceiveSms' => $contact->hasSettingsReceiveSms(),
            'settingsReceiveCalls' => $contact->hasSettingsReceiveCalls(),
            'metadataTags' => $tags,
        ]));
    }
}
