<?php

namespace App\Tests\Community\Printing;

use App\Billing\Model\OrderLine;
use App\Community\Printing\PrintingPriceCalculator;
use App\Entity\Community\PrintingCampaign;
use App\Entity\Community\PrintingOrder;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Theme\WebsiteTheme;
use App\Platform\Products;
use App\Tests\KernelTestCase;

class PrintingPriceCalculatorTest extends KernelTestCase
{
    public function provideCreateOrderLines()
    {
        yield 'posters-banners-unaddressed' => [
            'order' => $this->createOrderFixture('75007', false, false, [
                ['product' => Products::PRINT_OFFICIAL_POSTER, 'quantity' => 300],
                ['product' => Products::PRINT_OFFICIAL_BANNER, 'quantity' => 300],
            ]),
            'expected' => [
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Affiche officielle', '', 300, 1.5, 20.0),
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Bandeau d\'affiche', '', 300, 1.333333, 5.5),
                new OrderLine(OrderLine::TYPE_SHIPPING_FEE, 'Livraison', '', 1, 0, 20.0),
            ],
        ];

        yield 'ballots-pledges-unaddressed' => [
            'order' => $this->createOrderFixture('35420', false, false, [
                ['product' => Products::PRINT_OFFICIAL_BALLOT, 'quantity' => 150_000],
                ['product' => Products::PRINT_OFFICIAL_PLEDGE, 'quantity' => 75_000],
            ]),
            'expected' => [
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Bulletins de vote', '', 150_000, 0.0062, 5.5),
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Profession de foi', '', 75_000, 0.029067, 5.5),
                new OrderLine(OrderLine::TYPE_SHIPPING_FEE, 'Livraison', '', 1, 266.18, 20.0),
            ],
        ];

        yield 'posters-ballots-pledges-unaddressed' => [
            'order' => $this->createOrderFixture('35420', false, false, [
                ['product' => Products::PRINT_OFFICIAL_POSTER, 'quantity' => 300],
                ['product' => Products::PRINT_OFFICIAL_BALLOT, 'quantity' => 150_000],
                ['product' => Products::PRINT_OFFICIAL_PLEDGE, 'quantity' => 75_000],
            ]),
            'expected' => [
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Affiche officielle', '', 300, 1.5, 20.0),
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Bulletins de vote', '', 150_000, 0.0062, 5.5),
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Profession de foi', '', 75_000, 0.029067, 5.5),
                new OrderLine(OrderLine::TYPE_SHIPPING_FEE, 'Livraison', '', 1, 266.18, 20.0),
            ],
        ];

        yield 'flyers-booklet4-distributed-enveloped' => [
            'order' => $this->createOrderFixture('92110', true, false, [
                ['product' => Products::PRINT_CAMPAIGN_FLYER, 'quantity' => 24_210],
                ['product' => Products::PRINT_CAMPAIGN_BOOKLET_4, 'quantity' => 24_210],
            ]),
            'expected' => [
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Tract A5', '', 25_000, 0.0336, 5.5),
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Livret 4 pages', '', 25_000, 0.04, 5.5),
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Mise sous pli', '', 1, 12800.0, 20.0),
                new OrderLine(OrderLine::TYPE_SHIPPING_FEE, 'Livraison', '', 1, 69.68, 20.0),
            ],
        ];

        yield 'flyers-distributed-not-enveloped' => [
            'order' => $this->createOrderFixture('92110', false, false, [
                ['product' => Products::PRINT_CAMPAIGN_FLYER, 'quantity' => 25_000],
            ]),
            'expected' => [
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Tract A5', '', 25_000, 0.0336, 5.5),
                new OrderLine(OrderLine::TYPE_SHIPPING_FEE, 'Livraison', '', 1, 36.66, 20.0),
            ],
        ];

        yield 'booklet8-card-addressed-included' => [
            'order' => $this->createOrderFixture('92110', false, true, [
                ['product' => Products::PRINT_CAMPAIGN_BOOKLET_8, 'quantity' => 56_930],
                ['product' => Products::PRINT_CAMPAIGN_CARD, 'quantity' => 56_930],
            ]),
            'expected' => [
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Livret 8 pages', '', 50_000, 0.06, 5.5),
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Carte postale', '', 50_000, 0.0216, 5.5),
                new OrderLine(OrderLine::TYPE_PRODUCT, 'Mise sous pli', '', 1, 25600.0, 20.0),
                new OrderLine(OrderLine::TYPE_SHIPPING_FEE, 'Affranchissement', '', 1, 93000.0, 20.0),
            ],
        ];
    }

    /**
     * @dataProvider provideCreateOrderLines
     */
    public function testCreateOrderLines(PrintingOrder $order, array $expectedLines)
    {
        self::bootKernel();

        $lines = self::getContainer()->get(PrintingPriceCalculator::class)->createOrderLines($order);

        // Reset description to avoid testing it
        foreach ($lines as $line) {
            $reflection = new \ReflectionObject($line);
            $description = $reflection->getProperty('description');
            $description->setAccessible(true);
            $description->setValue($line, '');
        }

        $this->assertEquals($expectedLines, $lines);
    }

    private function createOrderFixture(string $zipCode, bool $useMediapost, bool $isAddressed, array $campaignsData): PrintingOrder
    {
        $order = PrintingOrder::createFixture([
            'project' => new Project(new Organization('Citipo'), 'Citipo', new WebsiteTheme(1, 1, 'citipo/theme')),
            'deliveryUseMediapost' => $useMediapost,
            'deliveryAddressed' => $isAddressed,
            'deliveryMainAddressName' => 'Titouan Galopin',
            'deliveryMainAddressStreet1' => '49 Rue de Ponthieu',
            'deliveryMainAddressZipCode' => $zipCode,
            'deliveryMainAddressCity' => 'Paris',
            'deliveryMainAddressCountry' => 'FR',
            'deliveryPosterAddressName' => 'Titouan Galopin',
            'deliveryPosterAddressStreet1' => '49 Rue de Ponthieu',
            'deliveryPosterAddressZipCode' => $zipCode,
            'deliveryPosterAddressCity' => 'Paris',
            'deliveryPosterAddressCountry' => 'FR',
        ]);

        foreach ($campaignsData as $data) {
            $order->getCampaigns()->add(PrintingCampaign::createFixture(array_merge($data, [
                'printingOrder' => $order,
            ])));
        }

        return $order;
    }
}
