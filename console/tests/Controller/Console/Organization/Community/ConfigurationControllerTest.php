<?php

namespace App\Tests\Controller\Console\Organization\Community;

use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Entity\OrganizationMainTag;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationRepository;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationControllerTest extends WebTestCase
{
    public function testMainTags()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/main-tags');
        $this->assertResponseIsSuccessful();

        /** @var Organization $organization */
        $organization = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_CITIPO_UUID);
        $this->assertFalse($organization->mainTagsIsProgress());

        $mainTags = $organization->getMainTags()->map(static fn (OrganizationMainTag $t) => $t->getTag()->getName())->toArray();
        sort($mainTags);
        $this->assertSame(['ExampleTag', 'StartWithTag', 'Tag'], $mainTags);

        /** @var Tag $tag */
        $tag = static::getContainer()->get(TagRepository::class)->findOneByName($organization, 'DontStartWithTag');

        $button = $crawler->selectButton('Save');
        $client->submit($button->form(), [
            'main_tags[tags][0]' => '',
            'main_tags[tags][1]' => '',
            'main_tags[tags][2]' => '',
            'main_tags[tags][3]' => $tag->getId(),
            'main_tags[tags][4]' => '',
            'main_tags[tags][5]' => '',
            'main_tags[tags][6]' => '',
            'main_tags[tags][7]' => '',
            'main_tags[isProgress]' => '1',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var Organization $organization */
        $organization = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid('219025aa-7fe2-4385-ad8f-31f386720d10');
        $this->assertTrue($organization->mainTagsIsProgress());
        $this->assertSame(['DontStartWithTag'], $organization->getMainTags()->map(static fn (OrganizationMainTag $t) => $t->getTag()->getName())->toArray());
    }

    public function testTagsList()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/tags');

        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('tbody tr'));

        $eventsNumber = $crawler->filter('tbody tr:nth-child(4) td:nth-child(2)');
        $this->assertSame('3', $eventsNumber->text());
    }

    public function provideTagsCreate(): iterable
    {
        yield ['NewCreatedTag'];
    }

    /**
     * @dataProvider provideTagsCreate
     */
    public function testTagsCreate(string $name)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $organization = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_CITIPO_UUID);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/tags');
        $crawler = $client->click($crawler->selectLink('New tag')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'tag[name]' => $name,
        ]);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertInstanceOf(Tag::class, static::getContainer()->get(TagRepository::class)->findOneBy([
            'organization' => $organization,
            'name' => $name,
        ]));

        $this->assertCount(8, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(5) td:nth-child(1)', $name);
    }

    public function testTagsCreateInvalidEmpty()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/tags');
        $crawler = $client->click($crawler->selectLink('New tag')->link());
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), ['tag[name]' => '']);
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('input[type="text"]');
        $this->assertSelectorExists('input.is-invalid');
    }

    public function provideTagsEdit(): iterable
    {
        yield ['ExampleTag', 'RenamedTag'];
    }

    /**
     * @dataProvider provideTagsEdit
     */
    public function testTagsEdit(string $oldName, string $newName)
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/tags');

        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(4) td:nth-child(1)', $oldName);

        $link = $crawler->filter('tbody tr:nth-child(4) td:nth-child(4) a:nth-child(3)')->link();
        $crawler = $client->click($link);

        $button = $crawler->selectButton('Save');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'tag[name]' => $newName,
        ]);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('tbody tr'));
        $this->assertSelectorTextContains('tbody tr:nth-child(4) td:nth-child(1)', $newName);
    }

    public function testTagsDelete()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/community/configure/tags');

        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('tbody tr'));

        $client->click($crawler->selectLink('Delete')->link());
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(6, $crawler->filter('tbody tr'));
    }
}
