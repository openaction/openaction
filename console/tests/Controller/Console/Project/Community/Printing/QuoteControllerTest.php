<?php

namespace App\Tests\Controller\Console\Project\Community\Printing;

use App\Billing\Invoice\GenerateQuotePdfMessage;
use App\Entity\Billing\Quote;
use App\Repository\Billing\QuoteRepository;
use App\Tests\WebTestCase;

class QuoteControllerTest extends WebTestCase
{
    public function testType()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/quote');
        $this->assertResponseIsSuccessful();
    }

    public function testOfficialForm()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/quote/official');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="quote"]')->form(), [
            'quote[deliveryStreet1]' => '4 Rue Chevert',
            'quote[deliveryStreet2]' => 'Etage 1',
            'quote[deliveryZipCode]' => '75007',
            'quote[deliveryCity]' => 'Paris',
            'quote[deliveryCountry]' => 'FR',
            'quote[billingOrganization]' => 'CPGT SAS',
            'quote[billingEmail]' => 'billing@citipo.com',
            'quote[billingStreet1]' => '49 Rue de Ponthieu',
            'quote[billingStreet2]' => 'Etage 1',
            'quote[billingZipCode]' => '75008',
            'quote[billingCity]' => 'Paris',
            'quote[billingCountry]' => 'FR',
            'quote[quantities][official_poster]' => 300,
            'quote[quantities][official_banner]' => 0,
            'quote[quantities][official_pledge]' => 75_000,
            'quote[quantities][official_ballot]' => 150_000,
        ]);
        $this->assertResponseRedirects();

        /** @var Quote $quote */
        $quote = self::getContainer()->get(QuoteRepository::class)->findOneBy([], ['createdAt' => 'DESC']);
        $this->assertSame(403503, $quote->getAmount());
        $this->assertNull($quote->getPdf());
        $this->assertNull($quote->getSentAt());
        $this->assertSame(
            [
                [
                    'type' => 'digital',
                    'name' => 'Affiche officielle',
                    'quantity' => 300,
                    'unitPrice' => 1.5,
                    'vatRate' => 20.0,
                ],
                [
                    'type' => 'digital',
                    'name' => 'Profession de foi',
                    'quantity' => 75_000,
                    'unitPrice' => 0.029067,
                    'vatRate' => 5.5,
                ],
                [
                    'type' => 'digital',
                    'name' => 'Bulletins de vote',
                    'quantity' => 150_000,
                    'unitPrice' => 0.0062,
                    'vatRate' => 5.5,
                ],
                [
                    'type' => 'shipping_fee',
                    'name' => 'Livraison',
                    'quantity' => 1,
                    'unitPrice' => 178.3,
                    'vatRate' => 20.0,
                ],
            ],
            array_map(
                static function ($a) {
                    unset($a['description']);

                    return $a;
                },
                $quote->getRawLines(),
            )
        );

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());
        /* @var GenerateQuotePdfMessage $message */
        $this->assertInstanceOf(GenerateQuotePdfMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($quote->getId(), $message->getQuoteId());
    }

    public function testCampaignForm()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/community/printing/quote/campaign');
        $this->assertResponseIsSuccessful();

        $client->submit($crawler->filter('form[name="quote"]')->form(), [
            'quote[deliveryStreet1]' => '4 Rue Chevert',
            'quote[deliveryStreet2]' => 'Etage 1',
            'quote[deliveryZipCode]' => '75007',
            'quote[deliveryCity]' => 'Paris',
            'quote[deliveryCountry]' => 'FR',
            'quote[billingOrganization]' => 'CPGT SAS',
            'quote[billingEmail]' => 'billing@citipo.com',
            'quote[billingStreet1]' => '49 Rue de Ponthieu',
            'quote[billingStreet2]' => 'Etage 1',
            'quote[billingZipCode]' => '75008',
            'quote[billingCity]' => 'Paris',
            'quote[billingCountry]' => 'FR',
            'quote[quantities][campaign_poster]' => 300,
            'quote[quantities][campaign_door]' => 1_000,
        ]);
        $this->assertResponseRedirects();

        /** @var Quote $quote */
        $quote = self::getContainer()->get(QuoteRepository::class)->findOneBy([], ['createdAt' => 'DESC']);
        $this->assertSame(163594, $quote->getAmount());
        $this->assertNull($quote->getPdf());
        $this->assertNull($quote->getSentAt());
        $this->assertSame(
            [
                [
                    'type' => 'digital',
                    'name' => 'Affiche de campagne',
                    'quantity' => 300,
                    'unitPrice' => 1.5,
                    'vatRate' => 20.0,
                ],
                [
                    'type' => 'digital',
                    'name' => 'Accroche-Porte',
                    'quantity' => 1_000,
                    'unitPrice' => 0.995,
                    'vatRate' => 5.5,
                ],
                [
                    'type' => 'shipping_fee',
                    'name' => 'Livraison',
                    'quantity' => 1,
                    'unitPrice' => 38.52,
                    'vatRate' => 20.0,
                ],
            ],
            array_map(
                static function ($a) {
                    unset($a['description']);

                    return $a;
                },
                $quote->getRawLines(),
            )
        );

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());
        /* @var GenerateQuotePdfMessage $message */
        $this->assertInstanceOf(GenerateQuotePdfMessage::class, $message = $messages[0]->getMessage());
        $this->assertSame($quote->getId(), $message->getQuoteId());
    }
}
