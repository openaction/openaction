<?php

namespace App\Tests\Controller\Console\Organization;

use App\Entity\Project;
use App\Platform\Features;
use App\Proxy\Consumer\ConfigureTrialSubdomainMessage;
use App\Repository\AreaRepository;
use App\Repository\Community\TagRepository;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;
use App\Util\Json;
use Symfony\Component\Mime\Email;

class ProjectsControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/projects');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-user]:contains("Titouan Galopin")');
        $this->assertSelectorExists('[data-global] .projects-item:contains("Citipo")');
        $this->assertSelectorExists('[data-global] .projects-item:contains("2 contacts")');
        $this->assertSelectorExists('[data-local] .projects-item:contains("Île-de-France")');
        $this->assertSelectorExists('[data-local] .projects-item:contains("1 contacts")');
    }

    public function provideOrganizations(): iterable
    {
        yield ['219025aa-7fe2-4385-ad8f-31f386720d10'];
    }

    /**
     * @dataProvider provideOrganizations
     */
    public function testCreateProjectForm(string $organization): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $client->request('GET', '/console/organization/'.$organization.'/projects');
        $client->clickLink('Launch a new global project');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/console/organization/'.$organization.'/projects');
        $client->clickLink('Launch a new local project');
        $this->assertResponseIsSuccessful();
    }

    public function provideProject(): iterable
    {
        yield 'global' => [
            '219025aa-7fe2-4385-ad8f-31f386720d10',
            [
                'name' => 'Awesome Project',
                'type' => 'global',
                'modules' => [Features::MODULE_WEBSITE],
                'tools' => [Features::TOOL_WEBSITE_PAGES, Features::TOOL_WEBSITE_POSTS],
            ],
        ];

        yield 'local' => [
            '219025aa-7fe2-4385-ad8f-31f386720d10',
            [
                'name' => 'Amazing Project',
                'type' => 'local',
                'areas' => ['fr_11'],
                'modules' => [Features::MODULE_WEBSITE, Features::MODULE_COMMUNITY],
                'tools' => [Features::TOOL_WEBSITE_PAGES, Features::TOOL_COMMUNITY_EMAILING],
            ],
        ];

        yield 'thematic' => [
            '219025aa-7fe2-4385-ad8f-31f386720d10',
            [
                'name' => 'Amazing Project',
                'type' => 'thematic',
                'tags' => ['StartWithTag'],
                'modules' => [Features::MODULE_WEBSITE, Features::MODULE_COMMUNITY],
                'tools' => [Features::TOOL_WEBSITE_PAGES, Features::TOOL_COMMUNITY_EMAILING],
            ],
        ];
    }

    /**
     * @dataProvider provideProject
     */
    public function testCreateProject(string $organization, array $data): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.$organization.'/project/create');
        $this->assertResponseIsSuccessful();

        $areaRepo = static::getContainer()->get(AreaRepository::class);
        $tagRepo = static::getContainer()->get(TagRepository::class);

        $areasIds = [];
        foreach ($data['areas'] ?? [] as $code) {
            $area = $areaRepo->findOneBy(['code' => $code]);
            $areaId = $area->getId();
            $areasIds[$areaId] = ['id' => $areaId, 'name' => $area->getName()];
        }

        $tagsIds = [];
        foreach ($data['tags'] ?? [] as $name) {
            $tag = $tagRepo->findOneBy(['name' => $name]);
            $tagId = $tag->getId();
            $tagsIds[] = ['id' => $tagId, 'name' => $name];
        }

        $button = $crawler->selectButton('Create');
        $client->submit($button->form(), [
            'create_project[name]' => $data['name'],
            'create_project[type]' => $data['type'],
            'create_project[areasIds]' => Json::encode($areasIds),
            'create_project[tags]' => Json::encode($tagsIds),
            'create_project[modules]' => $data['modules'],
            'create_project[tools]' => $data['tools'],
        ]);

        $this->assertResponseRedirects();

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => $data['name']]);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame($data['name'], $project->getName());
        $this->assertSame(count($data['areas'] ?? []) > 0, $project->isLocal());
        $this->assertCount(count($data['areas'] ?? []), $project->getAreas());
        $this->assertSame(count($data['tags'] ?? []) > 0, $project->isThematic());
        $this->assertCount(count($data['tags'] ?? []), $project->getTags());
        $this->assertSame($data['modules'], $project->getModules());
        $this->assertSame($data['tools'], $project->getTools());
        $this->assertSame('c4o.io', $project->getRootDomain()->getName());
        $this->assertSame('fr', $project->getWebsiteLocale());

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(2, $messages);

        /** @var ConfigureTrialSubdomainMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(ConfigureTrialSubdomainMessage::class, $message);
        $this->assertSame($project->getSubDomain(), $message->getSubdomain());

        // Test notification e-mail
        /** @var Email $message */
        $message = $this->getMailerMessage(0);
        $this->assertSame($message->getSubject(), 'A new project was created in your organization');
        $this->assertEmailAddressContains($message, 'to', 'titouan.galopin@citipo.com');

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h5:contains("'.$data['name'].'")');
    }

    public function testCreateProjectBatch(): void
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/organization/'.self::ORGA_CITIPO_UUID.'/project/create-batch');
        $this->assertResponseIsSuccessful();

        $area = static::getContainer()->get(AreaRepository::class)->findOneBy(['code' => 'fr_11']);

        $button = $crawler->selectButton('Create');
        $client->submit($button->form(), [
            'create_batch_project[items][0][name]' => 'Project 1 Local',
            'create_batch_project[items][0][adminEmail]' => 'project1admin@citipo.com',
            'create_batch_project[items][0][type]' => 'local',
            'create_batch_project[items][0][areasIds]' => Json::encode([$area->getId() => ['id' => $area->getId(), 'name' => $area->getName()]]),
            'create_batch_project[items][1][name]' => 'Project 2 Global',
            'create_batch_project[items][1][adminEmail]' => '',
            'create_batch_project[items][1][type]' => 'global',
            'create_batch_project[items][1][areasIds]' => Json::encode([]),
            'create_batch_project[items][2][name]' => '',
            'create_batch_project[items][2][adminEmail]' => '',
            'create_batch_project[items][2][type]' => 'global',
            'create_batch_project[items][2][areasIds]' => Json::encode([]),
        ]);

        $this->assertResponseRedirects();

        /** @var Project $project1 */
        $project1 = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => 'Project 1 Local']);
        $this->assertInstanceOf(Project::class, $project1);
        $this->assertSame('Project 1 Local', $project1->getName());
        $this->assertTrue($project1->isLocal());
        $this->assertCount(1, $project1->getAreas());
        $this->assertSame(['website', 'community'], $project1->getModules());
        $this->assertSame(
            ['website_pages', 'website_posts', 'website_documents', 'website_events', 'website_forms', 'website_newsletter', 'website_trombinoscope', 'website_manifesto', 'community_contacts', 'community_emailing', 'community_texting', 'community_phoning'],
            $project1->getTools()
        );
        $this->assertSame('c4o.io', $project1->getRootDomain()->getName());
        $this->assertSame('fr', $project1->getWebsiteLocale());

        /** @var Project $project2 */
        $project2 = static::getContainer()->get(ProjectRepository::class)->findOneBy(['name' => 'Project 2 Global']);
        $this->assertInstanceOf(Project::class, $project2);
        $this->assertSame('Project 2 Global', $project2->getName());
        $this->assertFalse($project2->isLocal());
        $this->assertCount(0, $project2->getAreas());
        $this->assertSame(['website', 'community'], $project2->getModules());
        $this->assertSame(
            ['website_pages', 'website_posts', 'website_documents', 'website_events', 'website_forms', 'website_newsletter', 'website_trombinoscope', 'website_manifesto', 'community_contacts', 'community_emailing', 'community_texting', 'community_phoning'],
            $project2->getTools()
        );
        $this->assertSame('c4o.io', $project2->getRootDomain()->getName());
        $this->assertSame('fr', $project2->getWebsiteLocale());

        // Test the dispatching of the message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $messages = $transport->get();
        $this->assertCount(5, $messages);

        /** @var ConfigureTrialSubdomainMessage $message */
        $message = $messages[0]->getMessage();
        $this->assertInstanceOf(ConfigureTrialSubdomainMessage::class, $message);
        $this->assertSame($project1->getSubDomain(), $message->getSubdomain());

        /** @var ConfigureTrialSubdomainMessage $message */
        $message = $messages[3]->getMessage();
        $this->assertInstanceOf(ConfigureTrialSubdomainMessage::class, $message);
        $this->assertSame($project2->getSubDomain(), $message->getSubdomain());

        // Test notification e-mails
        /** @var Email $message */
        $message = $this->getMailerMessage(0);
        $this->assertSame($message->getSubject(), 'A new project was created in your organization');
        $this->assertEmailAddressContains($message, 'to', 'titouan.galopin@citipo.com');

        /** @var Email $message */
        $message = $this->getMailerMessage(1);
        $this->assertSame($message->getSubject(), 'Titouan Galopin is waiting for you!');
        $this->assertEmailAddressContains($message, 'to', 'project1admin@citipo.com');

        /** @var Email $message */
        $message = $this->getMailerMessage(2);
        $this->assertSame($message->getSubject(), 'A new project was created in your organization');
        $this->assertEmailAddressContains($message, 'to', 'titouan.galopin@citipo.com');

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h5:contains("Project 1 Local")');
        $this->assertSelectorExists('h5:contains("Project 2 Global")');
    }
}
