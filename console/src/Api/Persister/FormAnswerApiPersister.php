<?php

namespace App\Api\Persister;

use App\Api\Model\ContactApiData;
use App\Api\Model\FormAnswerApiData;
use App\Community\Automation\EmailAutomationDispatcher;
use App\Entity\Community\Contact;
use App\Entity\Community\EmailAutomation;
use App\Entity\Website\Form;
use App\Entity\Website\FormAnswer;
use App\Entity\Website\FormBlock;
use App\Repository\Website\PetitionRepository;
use Doctrine\ORM\EntityManagerInterface;

use function Symfony\Component\String\u;

class FormAnswerApiPersister
{
    public function __construct(
        private readonly ContactApiPersister $contactPersister,
        private readonly EntityManagerInterface $em,
        private readonly EmailAutomationDispatcher $automationDispatcher,
        private readonly PetitionRepository $petitionRepository,
    ) {
    }

    public function persist(Form $form, FormAnswerApiData $data, ?Contact $linkedContact = null): FormAnswer
    {
        $rawFields = array_values($data->fields ?: []);

        // Automatically add the newsletter blocks if requested
        $blocks = $form->getBlocks()->toArray();
        if ($form->proposeNewsletter()) {
            if (!$form->hasEmailBlock()) {
                $blocks[] = new FormBlock($form, FormBlock::TYPE_EMAIL, 'Email', true);
            }

            $blocks[] = new FormBlock($form, FormBlock::TYPE_NEWSLETTER, 'Newsletter');
        }

        // Map the fields and try to map values to contact data
        $contactData = new ContactApiData();
        $mappedFields = [];

        $key = 0;
        foreach ($blocks as $block) {
            if (!$block->isField()) {
                continue;
            }

            $value = $rawFields[$key] ?? '';

            if (FormBlock::TYPE_NEWSLETTER === $block->getType()) {
                $value = '1' === $value ? 'Yes' : 'No';
            } elseif (FormBlock::TYPE_TAG_HIDDEN === $block->getType()) {
                $value = $block->getConfig()['tags'] ?? '';
            }

            $mappedFields[$block->getContent()] = $value;
            $this->mapAutomaticField($block->getType(), $contactData, $value);

            ++$key;
        }

        if ($contactData->metadataTags) {
            $contactData->metadataTags = array_filter(array_unique($contactData->metadataTags));
        }

        // If there is a linked contact, link it to the payload
        if ($linkedContact) {
            $this->linkContactToPayload($linkedContact, $contactData);
        }

        // Persist the contact if possible
        $contact = null;
        if ($contactData->email) {
            $contact = $this->contactPersister->persist($contactData, $form->getProject());
        }

        // Persist the answer
        $this->em->persist($answer = new FormAnswer($form, $contact, $mappedFields));
        $this->em->flush();

        // Update petition signature count if linked
        if ($form->getLocalizedPetition()) {
            $this->petitionRepository->synchronizeSignaturesCount($form->getLocalizedPetition()->getPetition());
        }

        // Trigger automations
        $orga = $form->getProject()->getOrganization();
        $this->automationDispatcher->dispatch(EmailAutomation::TRIGGER_NEW_FORM_ANSWER, $orga, $contact, $answer);

        return $answer;
    }

    private function linkContactToPayload(Contact $linkedContact, ContactApiData $contactData)
    {
        // If no email is provided in the payload or if the email provided is the same as before, map it
        if (!$contactData->email || Contact::normalizeEmail($contactData->email) === $linkedContact->getEmail()) {
            $contactData->email = $linkedContact->getEmail();

            return;
        }

        // If a different email is provided in the payload, check if the new email conflicts with an existing contact
        $conflictingContact = $this->em->getRepository(Contact::class)->findOneBy([
            'organization' => $linkedContact->getOrganization(),
            'email' => Contact::normalizeEmail($contactData->email),
        ]);

        // If there is a conflict, keep the original email
        if ($conflictingContact) {
            $contactData->email = $linkedContact->getEmail();

            return;
        }

        // Otherwise persist the new email and use it
        $linkedContact->changeEmail($contactData->email);
        $contactData->email = $linkedContact->getEmail();

        $this->em->persist($linkedContact);
        $this->em->flush();
    }

    private function mapAutomaticField(string $blockType, ContactApiData $contactData, string $value)
    {
        switch ($blockType) {
            case FormBlock::TYPE_EMAIL:
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $contactData->email = $value;
                }

                break;

            case FormBlock::TYPE_FORMAL_TITLE:
                $contactData->profileFormalTitle = u($value)->slice(0, 50)->toString();
                break;

            case FormBlock::TYPE_FIRST_NAME:
                $contactData->profileFirstName = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_MIDDLE_NAME:
                $contactData->profileMiddleName = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_LAST_NAME:
                $contactData->profileLastName = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_BIRTHDATE:
                $contactData->profileBirthdate = $value;
                break;

            case FormBlock::TYPE_GENDER:
                $contactData->profileGender = u($value)->slice(0, 20)->toString();
                break;

            case FormBlock::TYPE_NATIONALITY:
                $contactData->profileNationality = u($value)->slice(0, 2)->toString();
                break;

            case FormBlock::TYPE_COMPANY:
                $contactData->profileCompany = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_JOB_TITLE:
                $contactData->profileJobTitle = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_PHONE:
                $contactData->contactPhone = u($value)->slice(0, 50)->toString();
                break;

            case FormBlock::TYPE_WORK_PHONE:
                $contactData->contactWorkPhone = u($value)->slice(0, 50)->toString();
                break;

            case FormBlock::TYPE_SOCIAL_FACEBOOK:
                $contactData->socialFacebook = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_SOCIAL_TWITTER:
                $contactData->socialTwitter = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_SOCIAL_LINKEDIN:
                $contactData->socialLinkedIn = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_SOCIAL_TELEGRAM:
                $contactData->socialTelegram = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_SOCIAL_WHATSAPP:
                $contactData->socialWhatsapp = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_STREET_ADDRESS:
                $contactData->addressStreetLine1 = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_STREET_ADDRESS_2:
                $contactData->addressStreetLine2 = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_CITY:
                $contactData->addressCity = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_ZIP_CODE:
                $contactData->addressZipCode = u($value)->slice(0, 150)->toString();
                break;

            case FormBlock::TYPE_COUNTRY:
                $contactData->addressCountry = u($value)->slice(0, 50)->toString();
                break;

            case FormBlock::TYPE_NEWSLETTER:
                if ('Yes' === $value) {
                    $contactData->settingsReceiveNewsletters = true;
                }

                break;

            case FormBlock::TYPE_TAG_RADIO:
                $contactData->metadataTags[] = trim($value);
                break;

            case FormBlock::TYPE_TAG_CHECKBOX:
            case FormBlock::TYPE_TAG_HIDDEN:
                $contactData->metadataTags = array_merge($contactData->metadataTags ?: [], array_map('trim', explode(',', $value)));
                break;
        }
    }
}
