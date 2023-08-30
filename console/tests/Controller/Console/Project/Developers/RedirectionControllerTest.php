<?php

namespace App\Tests\Controller\Console\Project\Developers;

use App\Entity\Website\Redirection;
use App\Repository\Website\RedirectionRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\DomCrawler\Crawler;

class RedirectionControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/redirections');

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-box tbody tr'));
    }

    public function provideCreateValid(): iterable
    {
        yield [
            'source' => '/source',
            'target' => '/target',
            'code' => 302,
        ];

        yield [
            'source' => '/source/*',
            'target' => '/target',
            'code' => 302,
        ];

        yield [
            'source' => '/source',
            'target' => 'https://google.com',
            'code' => 302,
        ];
    }

    /**
     * @dataProvider provideCreateValid
     */
    public function testCreateValid(string $source, string $target, int $code)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/redirections/create');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'redirection[source]' => $source,
            'redirection[target]' => $target,
            'redirection[code]' => $code,
        ]);

        $this->assertResponseRedirects();

        /** @var Redirection $created */
        $created = static::getContainer()->get(RedirectionRepository::class)->findOneBy(['source' => $source]);
        $this->assertSame($source, $created->getSource());
        $this->assertSame($target, $created->getTarget());
        $this->assertSame($code, $created->getCode());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function provideCreateInvalid(): iterable
    {
        yield [
            'source' => str_pad('Source too long', 400, '-fail'),
            'target' => '/target',
            'code' => 302,
        ];

        yield [
            'source' => '/source',
            'target' => str_pad('Target too long', 400, '-fail'),
            'code' => 302,
        ];

        yield [
            'source' => '',
            'target' => '/target',
            'code' => 302,
        ];

        yield [
            'source' => '/source',
            'target' => '',
            'code' => 302,
        ];
    }

    /**
     * @dataProvider provideCreateInvalid
     */
    public function testCreateInvalid(string $source, string $target, int $code)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/redirections/create');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'redirection[source]' => $source,
            'redirection[target]' => $target,
            'redirection[code]' => $code,
        ]);

        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input.is-invalid');
    }

    public function provideEdit(): iterable
    {
        yield [
            'source' => '/source',
            'target' => '/target',
            'code' => 302,
        ];

        yield [
            'source' => '/source/*',
            'target' => '/target',
            'code' => 302,
        ];

        yield [
            'source' => '/source',
            'target' => 'https://google.com',
            'code' => 302,
        ];
    }

    /**
     * @dataProvider provideEdit
     */
    public function testEdit(string $source, string $target, int $code)
    {
        $client = static::createClient();
        $this->authenticate($client);

        /** @var Redirection $item */
        $item = static::getContainer()->get(RedirectionRepository::class)->findOneBy(['source' => '/redirection/dynamic/*/foo']);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/redirections/edit/'.$item->getId());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'redirection[source]' => $source,
            'redirection[target]' => $target,
            'redirection[code]' => $code,
        ]);

        $this->assertResponseRedirects();

        $item = static::getContainer()->get(RedirectionRepository::class)->find($item->getId());
        $this->assertSame($source, $item->getSource());
        $this->assertSame($target, $item->getTarget());
        $this->assertSame($code, $item->getCode());

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.world-box tbody tr'));
    }

    public function testDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/redirections');

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $row = $crawler->filter('.world-box tbody tr'));

        $client->click($row->filter('.btn-outline-danger')->link());
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.world-box tbody tr'));
    }

    public function testSort()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Fetch current order (use the last list, ie the footer, as the header has submenus and that messes with the order)
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/redirections');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $ids = $crawler->filter('td.world-table-sortable')->each(fn (Crawler $row) => $row->attr('data-id')));

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
            '/console/project/'.self::PROJECT_ACME_UUID.'/developers/redirections/sort?_token='.$token,
            ['data' => Json::encode($payload)]
        );

        $this->assertResponseIsSuccessful();

        // Check order was reversed (use the last list, ie the footer, as the header has submenus and that messes with the order)
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/developers/redirections');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $newIds = $crawler->filter('td.world-table-sortable')->each(fn (Crawler $row) => $row->attr('data-id')));
        $this->assertSame(array_reverse($ids), $newIds);
    }
}
