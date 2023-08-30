<?php

namespace App\Api\Transformer\Community;

use App\Api\Transformer\AbstractTransformer;
use App\Cdn\CdnRouter;
use App\Entity\Community\Contact;
use App\Util\PhoneNumber;
use App\Util\Uid;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

class ContactTransformer extends AbstractTransformer
{
    private CdnRouter $cdnRouter;

    public function __construct(CdnRouter $cdnRouter)
    {
        $this->cdnRouter = $cdnRouter;
    }

    public function transform(Contact $contact)
    {
        $pictureUrl = 'https://www.gravatar.com/avatar/'.($contact->getEmailHash() ?: 'none').'?d=mp&s=800';
        if ($contact->getPicture()) {
            $pictureUrl = $this->cdnRouter->generateUrl($contact->getPicture());
        }

        return [
            '_resource' => 'Contact',
            'id' => Uid::toBase62($contact->getUuid()),
            'email' => $contact->getEmail(),
            'picture' => $pictureUrl,
            'isMember' => $contact->isMember(),
            'profileFormalTitle' => $contact->getProfileFormalTitle(),
            'profileFirstName' => $contact->getProfileFirstName(),
            'profileMiddleName' => $contact->getProfileMiddleName(),
            'profileLastName' => $contact->getProfileLastName(),
            'profileBirthdate' => $contact->getProfileBirthdate()?->format('Y-m-d'),
            'profileGender' => $contact->getProfileGender(),
            'profileNationality' => $contact->getProfileNationality(),
            'profileCompany' => $contact->getProfileCompany(),
            'profileJobTitle' => $contact->getProfileJobTitle(),
            'contactPhone' => $contact->getContactPhone(),
            'contactWorkPhone' => $contact->getContactWorkPhone(),
            'parsedContactPhone' => PhoneNumber::format($contact->getParsedContactPhone()),
            'parsedContactWorkPhone' => PhoneNumber::format($contact->getParsedContactWorkPhone()),
            'socialFacebook' => $contact->getSocialFacebook(),
            'socialTwitter' => $contact->getSocialTwitter(),
            'socialLinkedIn' => $contact->getSocialLinkedIn(),
            'socialTelegram' => $contact->getSocialTelegram(),
            'socialWhatsapp' => $contact->getSocialWhatsapp(),
            'addressStreetLine1' => $contact->getAddressStreetLine1(),
            'addressStreetLine2' => $contact->getAddressStreetLine2(),
            'addressZipCode' => $contact->getAddressZipCode(),
            'addressCity' => $contact->getAddressCity(),
            'addressCountry' => $contact->getAddressCountry() ? strtoupper($contact->getAddressCountry()->getCode()) : null,
            'settingsReceiveNewsletters' => $contact->hasSettingsReceiveNewsletters(),
            'settingsReceiveSms' => $contact->hasSettingsReceiveSms(),
            'settingsReceiveCalls' => $contact->hasSettingsReceiveCalls(),
            'settingsByProject' => $contact->getSettingsByProject(),
            'metadataTags' => $contact->getMetadataTagsNames(),
            'metadataCustomFields' => $contact->getMetadataCustomFields(),
        ];
    }

    public static function describeResourceName(): string
    {
        return 'Contact';
    }

    public static function describeResourceSchema(): array
    {
        return [
            '_resource' => 'string',
            'id' => 'string',
            'email' => '?string',
            'picture' => 'string',
            'isMember' => 'boolean',
            'profileFormalTitle' => '?string',
            'profileFirstName' => '?string',
            'profileMiddleName' => '?string',
            'profileLastName' => '?string',
            'profileBirthdate' => '?string',
            'profileGender' => '?string',
            'profileNationality' => '?string',
            'profileCompany' => '?string',
            'profileJobTitle' => '?string',
            'contactPhone' => '?string',
            'contactWorkPhone' => '?string',
            'parsedContactPhone' => '?string',
            'parsedContactWorkPhone' => '?string',
            'socialFacebook' => '?string',
            'socialTwitter' => '?string',
            'socialLinkedIn' => '?string',
            'socialTelegram' => '?string',
            'socialWhatsapp' => '?string',
            'addressStreetLine1' => '?string',
            'addressStreetLine2' => '?string',
            'addressZipCode' => '?string',
            'addressCity' => '?string',
            'addressCountry' => '?string',
            'settingsReceiveNewsletters' => 'boolean',
            'settingsReceiveSms' => 'boolean',
            'settingsReceiveCalls' => 'boolean',
            'settingsByProject' => new Property([
                'type' => 'array',
                'items' => new Items([
                    'type' => 'object',
                    'properties' => [
                        new Property(['property' => 'projectName', 'type' => 'string']),
                        new Property(['property' => 'projectId', 'type' => 'string']),
                        new Property(['property' => 'settingsReceiveNewsletters', 'type' => 'boolean']),
                        new Property(['property' => 'settingsReceiveSms', 'type' => 'boolean']),
                        new Property(['property' => 'settingsReceiveCalls', 'type' => 'boolean']),
                    ],
                ]),
            ]),
            'metadataTags' => new Property(['type' => 'array', 'items' => new Items(['type' => 'string'])]),
            'metadataCustomFields' => [],
        ];
    }
}
