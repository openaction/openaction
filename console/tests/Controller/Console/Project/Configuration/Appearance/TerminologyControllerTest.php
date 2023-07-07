<?php

namespace App\Tests\Controller\Console\Project\Configuration\Appearance;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Tests\WebTestCase;

class TerminologyControllerTest extends WebTestCase
{
    public function testTerminology()
    {
        $client = static::createClient();
        $this->authenticate($client, 'titouan.galopin@citipo.com');

        $crawler = $client->request('GET', '/console/project/'.self::PROJECT_ACME_UUID.'/configuration/appearance/terminology');
        $button = $crawler->selectButton('Save');

        $client->submit($button->form(), [
            'terminology[posts]' => 'posts term',
            'terminology[events]' => 'events term',
            'terminology[trombinoscope]' => 'trombinoscope term',
            'terminology[manifesto]' => 'manifesto term',
            'terminology[newsletter]' => 'newsletter term',
            'terminology[acceptPrivacy]' => 'acceptPrivacy term',
            'terminology[socialNetworks]' => 'socialNetworks term',
        ]);

        /** @var Project $project */
        $project = static::getContainer()->get(ProjectRepository::class)->findOneBy(['uuid' => '2c720420-65fd-4360-9d77-731758008497']);
        $this->assertSame('posts term', $project->getAppearanceTerminology()->getPosts());
        $this->assertSame('events term', $project->getAppearanceTerminology()->getEvents());
        $this->assertSame('trombinoscope term', $project->getAppearanceTerminology()->getTrombinoscope());
        $this->assertSame('manifesto term', $project->getAppearanceTerminology()->getManifesto());
        $this->assertSame('newsletter term', $project->getAppearanceTerminology()->getNewsletter());
        $this->assertSame('acceptPrivacy term', $project->getAppearanceTerminology()->getAcceptPrivacy());
        $this->assertSame('socialNetworks term', $project->getAppearanceTerminology()->getSocialNetworks());

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.world-alert:contains("Appearance configuration was successfully saved.")');
    }
}
