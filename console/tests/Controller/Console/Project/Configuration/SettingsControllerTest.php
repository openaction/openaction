<?php

namespace App\Tests\Controller\Console\Project\Configuration;

use App\Entity\Organization;
use App\Entity\Project;
use App\Platform\Features;
use App\Repository\AreaRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class SettingsControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings');
        $this->assertResponseIsSuccessful();
    }

    public function testDomain()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/domain');
        $this->assertResponseIsSuccessful();
    }

    public function provideDetails(): iterable
    {
        yield 'global' => [
            'name' => 'Awesome Project',
            'type' => 'global',
            'locale' => 'fr',
            'areas' => [],
        ];

        yield 'local' => [
            'name' => 'Amazing Project',
            'type' => 'local',
            'locale' => 'en',
            'areas' => ['fr_11'],
        ];

        yield 'brazilian' => [
            'name' => 'Southern Project',
            'type' => 'global',
            'locale' => 'pt_BR',
            'areas' => [],
        ];
    }

    /**
     * @dataProvider provideDetails
     */
    public function testDetails(string $name, string $type, string $locale, array $areas)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/details');
        $this->assertResponseIsSuccessful();

        $areaRepo = static::getContainer()->get(AreaRepository::class);
        $areasIds = [];
        foreach ($areas as $k => $code) {
            $area = $areaRepo->findOneBy(['code' => $code]);
            $areaId = $area->getId();
            $areasIds[$areaId] = ['id' => $areaId, 'name' => $area->getName()];
        }

        $button = $crawler->selectButton('Save');
        $client->submit($button->form(), [
            'update_details[name]' => $name,
            'update_details[type]' => $type,
            'update_details[locale]' => $locale,
            'update_details[areasIds]' => Json::encode($areasIds),
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => 'e816bcc6-0568-46d1-b0c5-917ce4810a87']);
        $this->assertSame($name, $project->getName());
        $this->assertSame($locale, $project->getWebsiteLocale());
        $this->assertSame(count($areas) > 0, $project->isLocal());
        $this->assertCount(count($areas), $project->getAreas());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h5:contains("'.$name.'")');
    }

    public function provideModules(): iterable
    {
        yield 'website' => [
            'modules' => [Features::MODULE_WEBSITE],
            'tools' => [Features::TOOL_WEBSITE_PAGES],
        ];

        yield 'website_community' => [
            'modules' => [Features::MODULE_WEBSITE, Features::MODULE_COMMUNITY],
            'tools' => [Features::TOOL_WEBSITE_PAGES, Features::TOOL_COMMUNITY_EMAILING],
        ];

        yield 'website_community_members_area' => [
            'modules' => [Features::MODULE_WEBSITE, Features::MODULE_COMMUNITY, Features::MODULE_MEMBERS_AREA],
            'tools' => [Features::TOOL_WEBSITE_PAGES, Features::TOOL_COMMUNITY_EMAILING, Features::TOOL_MEMBERS_AREA_ACCOUNT],
        ];
    }

    /**
     * @dataProvider provideModules
     */
    public function testModules(array $modules, array $tools)
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/modules');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Save');
        $client->submit($button->form(), [
            'update_modules[modules]' => $modules,
            'update_modules[tools]' => $tools,
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => 'e816bcc6-0568-46d1-b0c5-917ce4810a87']);
        $this->assertSame($modules, $project->getModules());
        $this->assertSame($tools, $project->getTools());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h5:contains("'.$project->getName().'")');
    }

    public function testDeleteProject()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings');
        $link = $crawler->filter('a:contains("Delete this project")');
        $this->assertCount(1, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertEquals('/console/organization/'.self::ORGA_CITIPO_UUID.'/projects', $client->getResponse()->headers->get('Location'));

        $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/start');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDuplicate()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);
        $this->assertSame(4, $project->getOrganization()->getProjects()->count());

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings');
        $link = $crawler->filter('a:contains("Duplicate project")');
        $this->assertCount(1, $link);

        $client->request('GET', $link->link()->getUri().'invalid');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertEquals('/console/organization/'.self::ORGA_CITIPO_UUID.'/projects', $client->getResponse()->headers->get('Location'));

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_CITIPO_UUID);
        $this->assertSame(5, $project->getOrganization()->getProjects()->count());
    }

    public function testMove()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/settings/move');
        $this->assertResponseIsSuccessful();

        /** @var Organization $citipoOrga */
        $citipoOrga = static::getContainer()->get(OrganizationRepository::class)->findOneByUuid(self::ORGA_CITIPO_UUID);

        $form = $crawler->selectButton('Move')->form();
        $client->submit($form, ['move_project[into]' => $citipoOrga->getId()]);

        // Check new location
        $project = static::getContainer()->get(ProjectRepository::class)->findOneByUuid(self::PROJECT_ACME_UUID);
        $this->assertSame($citipoOrga->getId(), $project->getOrganization()->getId());

        // Check redirect
        $location = $client->getResponse()->headers->get('Location');
        $this->assertSame('/console/organization/'.self::ORGA_CITIPO_UUID.'/projects', $location);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLegalities()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_CITIPO_UUID.'/configuration/settings/legalities');
        $this->assertResponseIsSuccessful();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => 'e816bcc6-0568-46d1-b0c5-917ce4810a87']);
        $this->assertNull($project->getLegalGdprName());
        $this->assertNull($project->getLegalGdprAddress());
        $this->assertNull($project->getLegalGdprEmail());
        $this->assertNull($project->getLegalPublisherName());
        $this->assertNull($project->getLegalPublisherRole());

        $button = $crawler->selectButton('Save');
        $client->submit($button->form(), [
            'update_legalities[legalGdprName]' => 'CPGT SAS',
            'update_legalities[legalGdprAddress]' => '49 Rue de Ponthieu 75008 Paris',
            'update_legalities[legalGdprEmail]' => 'dpo@citipo.com',
            'update_legalities[legalPublisherName]' => 'Titouan Galopin',
            'update_legalities[legalPublisherRole]' => 'President',
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => 'e816bcc6-0568-46d1-b0c5-917ce4810a87']);
        $this->assertSame('CPGT SAS', $project->getLegalGdprName());
        $this->assertSame('49 Rue de Ponthieu 75008 Paris', $project->getLegalGdprAddress());
        $this->assertSame('dpo@citipo.com', $project->getLegalGdprEmail());
        $this->assertSame('Titouan Galopin', $project->getLegalPublisherName());
        $this->assertSame('President', $project->getLegalPublisherRole());

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
