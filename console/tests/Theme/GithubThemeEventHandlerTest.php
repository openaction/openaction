<?php

namespace App\Tests\Theme;

use App\Entity\Theme\WebsiteTheme;
use App\Repository\Theme\WebsiteThemeRepository;
use App\Tests\KernelTestCase;
use App\Theme\Consumer\SyncThemeMessage;
use App\Theme\GithubThemeEventHandler;
use Doctrine\ORM\EntityManagerInterface;

class GithubThemeEventHandlerTest extends KernelTestCase
{
    private const FIXTURES_DIR = __DIR__.'/../Fixtures/github_website_themes';

    public function testWebsiteInvalidSignature()
    {
        self::bootKernel();

        $this->assertFalse($this->callWebsiteThemeEventHandler(
            'installation',
            'created',
            'sha256=invalid'
        ));
    }

    public function testWebsiteInstallationCreated()
    {
        self::bootKernel();

        $this->assertTrue($this->callWebsiteThemeEventHandler(
            'installation',
            'created',
            'sha256=82f4148c2f474c4e32660c6d652277adfe2fae8aa31608f684d6c9ddee5daf6e'
        ));

        /** @var WebsiteTheme $theme */
        $theme = $this->getWebsiteThemeRepository()->findOneBy(['repositoryFullName' => 'citipo/enviediledefrance.fr']);

        $this->assertInstanceOf(WebsiteTheme::class, $theme);
        $this->assertSame('citipo/enviediledefrance.fr', $theme->getRepositoryFullName());
        $this->assertSame('MDEwOlJlcG9zaXRvcnkzNTA4NzQyOTY=', $theme->getRepositoryNodeId());
        $this->assertSame('20980324', $theme->getInstallationId());
        $this->assertSame([], $theme->getName());
        $this->assertSame([], $theme->getDescription());
        $this->assertSame([], $theme->getTemplates());
        $this->assertNull($theme->getThumbnail());
        $this->assertNull($theme->getAuthor());
        $this->assertNull($theme->getUpdateError());
        $this->assertCount(0, $theme->getForOrganizations());
        $this->assertCount(0, $theme->getAssets());
    }

    public function testWebsiteInstallationDeleted()
    {
        self::bootKernel();

        $themes = $this->getWebsiteThemeRepository()->findBy(['installationId' => '20980257'], ['repositoryFullName' => 'ASC']);
        $this->assertSame('citipo/theme-bold', $themes[0]->getRepositoryFullName());
        $this->assertSame('R_kgDOGcPqGw', $themes[0]->getRepositoryNodeId());
        $this->assertSame('20980257', $themes[0]->getInstallationId());
        $this->assertSame('citipo/theme-efficient', $themes[1]->getRepositoryFullName());
        $this->assertSame('f73a0dddcac8', $themes[1]->getRepositoryNodeId());
        $this->assertSame('20980257', $themes[1]->getInstallationId());
        $this->assertSame('citipo/theme-structured', $themes[2]->getRepositoryFullName());
        $this->assertSame('MDEwOlJlcG9zaXRvcnkzNDc0MDE2OTY=', $themes[2]->getRepositoryNodeId());
        $this->assertSame('20980257', $themes[2]->getInstallationId());

        $this->assertTrue($this->callWebsiteThemeEventHandler(
            'installation',
            'deleted',
            'sha256=4d4153d19eddf4b7915c9aae9cef272b2a572374b99cf7ea084e4a00e748b8bc'
        ));

        static::getContainer()->get(EntityManagerInterface::class)->refresh($themes[0]);
        $this->assertNull($themes[0]->getRepositoryFullName());
        $this->assertNull($themes[0]->getRepositoryNodeId());
        $this->assertNull($themes[0]->getInstallationId());

        static::getContainer()->get(EntityManagerInterface::class)->refresh($themes[1]);
        $this->assertNull($themes[1]->getRepositoryFullName());
        $this->assertNull($themes[1]->getRepositoryNodeId());
        $this->assertNull($themes[1]->getInstallationId());

        static::getContainer()->get(EntityManagerInterface::class)->refresh($themes[2]);
        $this->assertNull($themes[2]->getRepositoryFullName());
        $this->assertNull($themes[2]->getRepositoryNodeId());
        $this->assertNull($themes[2]->getInstallationId());
    }

    public function testWebsiteInstallationRepositoriesAdded()
    {
        self::bootKernel();

        $this->assertTrue($this->callWebsiteThemeEventHandler(
            'installation_repositories',
            'added',
            'sha256=6e4bf37058970e406736a6a1d80326a0b374316222c9220822348971db9037f6'
        ));

        /** @var WebsiteTheme $theme */
        $theme = $this->getWebsiteThemeRepository()->findOneBy(['repositoryFullName' => 'citipo/sophiecluzel2021.fr']);

        $this->assertInstanceOf(WebsiteTheme::class, $theme);
        $this->assertSame('citipo/sophiecluzel2021.fr', $theme->getRepositoryFullName());
        $this->assertSame('MDEwOlJlcG9zaXRvcnkzNTY1NDIyNjY=', $theme->getRepositoryNodeId());
        $this->assertSame('20980257', $theme->getInstallationId());
        $this->assertSame([], $theme->getName());
        $this->assertSame([], $theme->getDescription());
        $this->assertSame([], $theme->getTemplates());
        $this->assertNull($theme->getThumbnail());
        $this->assertNull($theme->getAuthor());
        $this->assertNull($theme->getUpdateError());
        $this->assertCount(0, $theme->getForOrganizations());
        $this->assertCount(0, $theme->getAssets());
    }

    public function testWebsiteInstallationRepositoriesRemoved()
    {
        self::bootKernel();

        $theme = $this->getWebsiteThemeRepository()->findOneBy(['repositoryFullName' => 'citipo/theme-structured']);
        $this->assertSame('citipo/theme-structured', $theme->getRepositoryFullName());
        $this->assertSame('MDEwOlJlcG9zaXRvcnkzNDc0MDE2OTY=', $theme->getRepositoryNodeId());
        $this->assertSame('20980257', $theme->getInstallationId());

        $this->assertTrue($this->callWebsiteThemeEventHandler(
            'installation_repositories',
            'removed',
            'sha256=2e08a20fd7933ee4a48037a01649943b130c5e246c7c7d1b5109a939e9e35af0'
        ));

        static::getContainer()->get(EntityManagerInterface::class)->refresh($theme);
        $this->assertNull($theme->getRepositoryFullName());
        $this->assertNull($theme->getRepositoryNodeId());
        $this->assertNull($theme->getInstallationId());
    }

    public function testWebsitePushMain()
    {
        self::bootKernel();

        $this->assertTrue($this->callWebsiteThemeEventHandler(
            'push',
            'main',
            'sha256=6019e08b6aba8a933f0124d14f0f7d3283b415aa79ee2717e23fae06792f6f67'
        ));

        /** @var WebsiteTheme $theme */
        $theme = $this->getWebsiteThemeRepository()->findOneBy(['repositoryFullName' => 'citipo/theme-bold']);

        // Should have published sync message
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(1, $messages = $transport->get());
        $this->assertInstanceOf(SyncThemeMessage::class, $messages[0]->getMessage());
        $this->assertSame($theme->getId(), $messages[0]->getMessage()->getThemeId());
    }

    public function testWebsitePushNonMain()
    {
        self::bootKernel();

        $this->assertTrue($this->callWebsiteThemeEventHandler(
            'push',
            'non_main',
            'sha256=60a94b46d5a1f6872accd7da2852fcb3beca3f1de1d67e057f1c140e1c32bec9'
        ));

        // Should not have published sync message (non-default branch)
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');
        $this->assertCount(0, $transport->get());
    }

    private function getWebsiteThemeRepository(): WebsiteThemeRepository
    {
        return static::getContainer()->get(WebsiteThemeRepository::class);
    }

    private function callWebsiteThemeEventHandler(string $eventName, string $action, string $signature): bool
    {
        $handler = static::getContainer()->get(GithubThemeEventHandler::class);
        $payload = trim(file_get_contents(self::FIXTURES_DIR.'/'.$eventName.($action ? '_'.$action : '').'.json'));

        return $handler->handleWebsiteThemeEvent($eventName, $payload, $signature);
    }
}
