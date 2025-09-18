<?php

namespace App\Tests\Controller\Console\Project\Website;

use App\Entity\Website\LocalizedPetition;
use App\Repository\Website\LocalizedPetitionRepository;
use App\Tests\WebTestCase;

class LocalizedPetitionControllerTest extends WebTestCase
{
    private const PETITION_UUID = '2b91e3e0-7e2a-4da9-8a8a-9b9b12123456';
    private const LOCALIZED_EN_UUID = 'd1a1a1a1-a1a1-4b4b-8c8c-aaaaaaaaaaaa';

    public function testEditDropdownShowsLocales()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();

        // The dropdown menu should list existing EN and create buttons for other locales
        $this->assertGreaterThan(0, $crawler->filter('.dropdown-menu')->count());
        $menu = $crawler->filter('.dropdown-menu')->first();

        // Existing EN localization should have an edit link (by UUID)
        $this->assertGreaterThan(
            0,
            $menu->filter(sprintf('a[href*="%s"]', self::LOCALIZED_EN_UUID))->count()
        );

        // Create buttons for other locales should be present (identified by locale in path)
        foreach (['fr', 'de', 'it', 'nl', 'pt'] as $locale) {
            $this->assertGreaterThan(
                0,
                $menu->filter(sprintf("a.dropdown-item[href*='/localized/%s/create']", $locale))->count(),
                sprintf('Expected create link for locale %s', $locale)
            );
        }

        // Deletion options are only visible when multiple localizations exist; not asserted here
    }

    public function testEditPageRendersExisting()
    {
        $client = static::createClient();
        $this->authenticate($client);

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input#petition-title');
    }

    public function testCreateLocalizationAndRedirect()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Go to petitions list and click Create FR
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();

        // Click on the create FR link (identified by locale in path)
        $createFr = $crawler->filter(".dropdown-menu a.dropdown-item[href*='/localized/fr/create']")->first();
        $this->assertGreaterThan(0, $createFr->count());
        $crawler = $client->click($createFr->link());
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input#petition-title');

        /** @var LocalizedPetitionRepository $repo */
        $repo = static::getContainer()->get(LocalizedPetitionRepository::class);
        /** @var LocalizedPetition|null $created */
        $created = $repo->findOneBy(['locale' => 'fr']);
        $this->assertNotNull($created);
        $this->assertEquals('fr', $created->getLocale());
        $this->assertEquals(self::PETITION_UUID, (string) $created->getPetition()->getUuid());
    }

    public function testUpdateParentDetails()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Open edit page to get global CSRF token
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $token = $this->filterGlobalCsrfToken($crawler);

        // Pick one author from project
        $project = static::getContainer()->get(\App\Repository\ProjectRepository::class)
            ->findOneBy(['uuid' => self::PROJECT_ACME_UUID]);
        $persons = static::getContainer()->get(\App\Repository\Website\TrombinoscopePersonRepository::class)
            ->getProjectPersonsList($project);
        $authorId = $persons[0]['id'] ?? null;

        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/update/parent?_token='.$token,
            [
                'petition' => [
                    'slug' => 'save-the-lake',
                    'startAt' => '2020-01-01T00:00:00+00:00',
                    'endAt' => '2020-02-01T00:00:00+00:00',
                    'signaturesGoal' => 1000,
                    'authors' => json_encode([$authorId]),
                ],
            ]
        );

        $this->assertResponseIsSuccessful();

        /** @var LocalizedPetitionRepository $locRepo */
        $locRepo = static::getContainer()->get(LocalizedPetitionRepository::class);
        /** @var LocalizedPetition $loc */
        $loc = $locRepo->findOneBy(['uuid' => self::LOCALIZED_EN_UUID]);
        $petition = $loc->getPetition();

        $this->assertSame('save-the-lake', $petition->getSlug());
        $this->assertNotNull($petition->getStartAt());
        $this->assertNotNull($petition->getEndAt());
        $this->assertSame(1000, $petition->getSignaturesGoal());
        $this->assertGreaterThanOrEqual(1, $petition->getAuthors()->count());
    }

    public function testUpdateParentPublishToggle()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Open edit page to get global CSRF token
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $token = $this->filterGlobalCsrfToken($crawler);

        // Publish now
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:sP');
        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/update/parent?_token='.$token,
            [
                'petition' => [
                    'publishedAt' => $now,
                ],
            ]
        );
        $this->assertResponseIsSuccessful();

        /** @var LocalizedPetitionRepository $locRepo */
        $locRepo = static::getContainer()->get(LocalizedPetitionRepository::class);
        /** @var LocalizedPetition $loc */
        $loc = $locRepo->findOneBy(['uuid' => self::LOCALIZED_EN_UUID]);
        $this->assertNotNull($loc->getPetition()->getPublishedAt());

        // Unpublish
        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/update/parent?_token='.$token,
            [
                'petition' => [
                    'publishedAt' => '',
                ],
            ]
        );
        $this->assertResponseIsSuccessful();

        $loc = $locRepo->findOneBy(['uuid' => self::LOCALIZED_EN_UUID]);
        $this->assertNull($loc->getPetition()->getPublishedAt());
    }

    public function testUpdateContentOnPublishedPetition()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Open edit page to get CSRF token
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $token = $this->filterGlobalCsrfToken($crawler);

        // Update localized content
        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/update/content?_token='.$token,
            [
                'localized_petition' => [
                    'title' => 'Updated Title',
                    'content' => '<p>Updated</p>',
                ],
            ]
        );
        $this->assertResponseIsSuccessful();
    }

    public function testUpdateLocalizedMetadataWithLegalities()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Open edit page to get CSRF token
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/edit');
        $this->assertResponseIsSuccessful();
        $token = $this->filterGlobalCsrfToken($crawler);

        // Update localized metadata including legalities
        $client->request(
            'POST',
            '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions/localized/'.self::LOCALIZED_EN_UUID.'/update/metadata?_token='.$token,
            [
                'localized_petition' => [
                    'description' => 'New description',
                    'submitButtonLabel' => 'Sign now',
                    'optinLabel' => 'I agree to be contacted',
                    'legalities' => '<p>Some legalities</p>',
                    'addressedTo' => 'City Council',
                    'categories' => json_encode([]),
                ],
            ]
        );
        $this->assertResponseIsSuccessful();

        /** @var LocalizedPetitionRepository $repo */
        $repo = static::getContainer()->get(LocalizedPetitionRepository::class);
        /** @var LocalizedPetition $loc */
        $loc = $repo->findOneBy(['uuid' => self::LOCALIZED_EN_UUID]);
        $this->assertSame('New description', $loc->getDescription());
        $this->assertSame('Sign now', $loc->getSubmitButtonLabel());
        $this->assertSame('I agree to be contacted', $loc->getOptinLabel());
        $this->assertSame('<p>Some legalities</p>', $loc->getLegalities());
        $this->assertSame('City Council', $loc->getAddressedTo());
    }

    public function testDeleteLocalized()
    {
        $client = static::createClient();
        $this->authenticate($client);

        // Ensure there are at least 2 localizations (create FR first)
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();
        $createFr = $crawler->filter(".dropdown-menu a.dropdown-item[href*='/localized/fr/create']")->first();
        $this->assertGreaterThan(0, $createFr->count());
        $client->click($createFr->link());
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Back to list and click Delete EN (link identified by localized UUID)
        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/website/petitions');
        $this->assertResponseIsSuccessful();
        $link = $crawler->filter(sprintf('a.text-danger[href*="%s"]', self::LOCALIZED_EN_UUID))->first();
        $this->assertGreaterThan(0, $link->count());
        $client->click($link->link());

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Ensure the EN localization no longer exists
        $repo = static::getContainer()->get(LocalizedPetitionRepository::class);
        $this->assertNull($repo->findOneBy(['uuid' => self::LOCALIZED_EN_UUID]));
    }
}
