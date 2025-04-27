<?php

namespace App\Tests\Controller\Bridge;

use App\Repository\Community\ContactRepository;
use App\Repository\Community\EmailingCampaignMessageRepository;
use App\Repository\Community\EmailingCampaignRepository;
use App\Tests\WebTestCase;

class ListUnsubscribeControllerTest extends WebTestCase
{
    public function testWebhookContactUuid()
    {
        $client = static::createClient();
        $client->request('POST', '/webhook/list-unsubscribe/104SKb9m0xnYyt8OiWn3ks');
        $this->assertResponseRedirects('https://citipo.com/newsletter?unsubscribe=1');

        /** @var ContactRepository $repository */
        $repository = static::getContainer()->get(ContactRepository::class);
        $contact = $repository->findOneBy(['uuid' => '20e51b91-bdec-495d-854d-85d6e74fc75e']);

        $this->assertFalse($contact->hasSettingsReceiveNewsletters());
    }

    public function testWebhookMessageId()
    {
        $client = static::createClient();

        /** @var EmailingCampaignRepository $campaignRepository */
        $campaignRepository = static::getContainer()->get(EmailingCampaignRepository::class);
        $campaign = $campaignRepository->findOneBy(['uuid' => '95b3f576-c643-45ba-9d5e-c9c44f65fab8']);

        /** @var ContactRepository $contactRepository */
        $contactRepository = static::getContainer()->get(ContactRepository::class);
        $contact = $contactRepository->findOneBy(['uuid' => '20e51b91-bdec-495d-854d-85d6e74fc75e']);

        /** @var EmailingCampaignMessageRepository $messageRepository */
        $messageRepository = static::getContainer()->get(EmailingCampaignMessageRepository::class);
        $message = $messageRepository->findOneBy(['contact' => $contact, 'campaign' => $campaign]);

        $client->request('POST', '/webhook/list-unsubscribe/'.$message->getId());
        $this->assertResponseRedirects('https://citipo.com/newsletter?unsubscribe=1');

        /** @var ContactRepository $contactRepository */
        $contactRepository = static::getContainer()->get(ContactRepository::class);
        $contact = $contactRepository->findOneBy(['uuid' => '20e51b91-bdec-495d-854d-85d6e74fc75e']);

        /** @var EmailingCampaignMessageRepository $messageRepository */
        $messageRepository = static::getContainer()->get(EmailingCampaignMessageRepository::class);
        $message = $messageRepository->findOneBy(['contact' => $contact, 'campaign' => $campaign]);

        $this->assertFalse($contact->hasSettingsReceiveNewsletters());
        $this->assertTrue($message->isUnsubscribed());
    }
}
