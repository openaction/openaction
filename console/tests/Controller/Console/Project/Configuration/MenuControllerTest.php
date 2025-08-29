<?php

namespace App\Tests\Controller\Console\Project\Configuration;

use App\Entity\Website\MenuItem;
use App\Repository\Website\MenuItemRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\DomCrawler\Crawler;

class MenuControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu');

        $this->assertResponseIsSuccessful();
        $this->assertCount(10, $crawler->filter('.world-box li'));
    }

    public function provideCreateValid(): iterable
    {
        yield [
            'position' => 'header',
            'parent' => null,
            'label' => 'No parent',
            'url' => '/no-parent',
            'newTab' => false,
        ];

        yield [
            'position' => 'header',
            'parent' => 'Your candidate',
            'label' => 'With parent',
            'url' => '/with-parent',
            'newTab' => false,
        ];

        yield [
            'position' => 'footer',
            'parent' => null,
            'label' => 'New tab',
            'url' => '/new-tab',
            'newTab' => true,
        ];
    }

    /**
     * @dataProvider provideCreateValid
     */
    public function testCreateValid(string $position, ?string $parentLabel, string $label, string $url, bool $newTab)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $parent = '';
        if ($parentLabel) {
            $parent = static::getContainer()->get(MenuItemRepository::class)->findOneBy(['label' => $parentLabel])->getId();
        }

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu/create/'.$position);
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'website_menu_item[parent]' => $parent,
            'website_menu_item[label]' => $label,
            'website_menu_item[url]' => $url,
            'website_menu_item[openNewTab]' => $newTab,
        ]);

        $this->assertResponseRedirects();

        /** @var MenuItem $created */
        $created = static::getContainer()->get(MenuItemRepository::class)->findOneBy(['label' => $label]);
        $this->assertSame($position, $created->getPosition());
        $this->assertSame($url, $created->getUrl());
        $this->assertSame($newTab, $created->isOpenNewTab());
        $this->assertSame($parent, $created->getParent() ? $created->getParent()->getId() : '');

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function provideCreateInvalid(): iterable
    {
        yield [
            'label' => '',
            'url' => '/empty-label',
        ];

        yield [
            'label' => 'Empty URL',
            'url' => '',
        ];
    }

    /**
     * @dataProvider provideCreateInvalid
     */
    public function testCreateInvalid(string $label, string $url)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu/create/header');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'website_menu_item[label]' => $label,
            'website_menu_item[url]' => $url,
        ]);

        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input.is-invalid');
    }

    public function provideEdit(): iterable
    {
        yield [
            'parent' => null,
            'label' => 'No parent',
            'url' => '/no-parent',
            'newTab' => false,
        ];

        yield [
            'parent' => 'Legalities',
            'label' => 'With parent',
            'url' => '/with-parent',
            'newTab' => false,
        ];

        yield [
            'parent' => null,
            'label' => 'New tab',
            'url' => '/new-tab',
            'newTab' => true,
        ];
    }

    /**
     * @dataProvider provideEdit
     */
    public function testEdit(?string $parentLabel, string $label, string $url, bool $newTab)
    {
        $client = static::createClient();
        $this->authenticate($client);

        /** @var MenuItem $item */
        $item = static::getContainer()->get(MenuItemRepository::class)->findOneBy(['label' => 'Privacy policy']);

        $parent = '';
        if ($parentLabel) {
            $parent = static::getContainer()->get(MenuItemRepository::class)->findOneBy(['label' => $parentLabel])->getId();
        }

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu/edit/'.$item->getId());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'website_menu_item[parent]' => $parent,
            'website_menu_item[label]' => $label,
            'website_menu_item[url]' => $url,
            'website_menu_item[openNewTab]' => $newTab,
        ]);

        $this->assertResponseRedirects();

        $item = static::getContainer()->get(MenuItemRepository::class)->find($item->getId());
        $this->assertSame($url, $item->getUrl());
        $this->assertSame($newTab, $item->isOpenNewTab());
        $this->assertSame($parent, $item->getParent() ? $item->getParent()->getId() : '');

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(10, $crawler->filter('.world-box li'));
    }

    public function provideEditInvalid(): iterable
    {
        yield [
            'label' => '',
            'url' => '/empty-label',
        ];

        yield [
            'label' => 'Empty URL',
            'url' => '',
        ];
    }

    /**
     * @dataProvider provideEditInvalid
     */
    public function testEditInvalid(string $label, string $url)
    {
        $client = static::createClient();
        $this->authenticate($client);

        /** @var MenuItem $item */
        $item = static::getContainer()->get(MenuItemRepository::class)->findOneBy(['label' => 'Privacy policy']);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu/edit/'.$item->getId());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'website_menu_item[label]' => $label,
            'website_menu_item[url]' => $url,
        ]);

        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input.is-invalid');
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu');

        $this->assertResponseIsSuccessful();
        $this->assertCount(10, $row = $crawler->filter('.world-box li'));

        $client->click($row->filter('.btn-outline-danger')->link());
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(9, $crawler->filter('.world-box li'));
    }

    public function testSort()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Fetch current order (use the last list, ie the footer, as the header has submenus and that messes with the order)
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu');
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $ids = $crawler->filter('.world-list')->last()->filter('li')->each(fn (Crawler $row) => $row->attr('data-id')));

        // Reverse the order
        $token = $this->filterGlobalCsrfToken($crawler);

        $payload = [];
        $i = 1;
        foreach (array_reverse($ids) as $id) {
            $payload[] = ['id' => (int) $id, 'order' => $i];
            ++$i;
        }

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu/sort/header?_token='.$token,
            ['data' => Json::encode($payload)]
        );

        $this->assertResponseIsSuccessful();

        // Check order was reversed (use the last list, ie the footer, as the header has submenus and that messes with the order)
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/menu');
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $newIds = $crawler->filter('.world-list')->last()->filter('li')->each(fn (Crawler $row) => $row->attr('data-id')));
        $this->assertSame(array_reverse($ids), $newIds);
    }
}
