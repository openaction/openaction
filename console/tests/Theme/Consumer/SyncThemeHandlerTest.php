<?php

namespace App\Tests\Theme\Consumer;

use App\Bridge\Github\GithubInterface;
use App\Bridge\Github\MockGithub;
use App\Entity\Theme\WebsiteTheme;
use App\Entity\Theme\WebsiteThemeAsset;
use App\Entity\Upload;
use App\Repository\Theme\WebsiteThemeRepository;
use App\Tests\KernelTestCase;
use App\Theme\Consumer\SyncThemeHandler;
use App\Theme\Consumer\SyncThemeMessage;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SyncThemeHandlerTest extends KernelTestCase
{
    public function testHandleValid()
    {
        $finder = Finder::create()->files()->in(__DIR__.'/../../Fixtures/github_website_themes/repo');
        $files = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $files[$file->getRelativePathname()] = $file->getContents();
        }

        /** @var MockGithub $github */
        $github = static::getContainer()->get(GithubInterface::class);
        $github->files = ['20980257' => ['citipo/theme-bold' => $files]];

        /** @var SyncThemeHandler $handler */
        $handler = static::getContainer()->get(SyncThemeHandler::class);

        /** @var WebsiteTheme $theme */
        $theme = static::getContainer()->get(WebsiteThemeRepository::class)->findOneBy([
            'repositoryFullName' => 'citipo/theme-bold',
        ]);

        $handler(new SyncThemeMessage($theme->getId()));

        static::getContainer()->get(EntityManagerInterface::class)->refresh($theme);

        $this->assertNull($theme->getUpdateError());
        $this->assertSame('Name FR', $theme->getName()['fr']);
        $this->assertSame('Name EN', $theme->getName()['en']);
        $this->assertSame('Description FR', $theme->getDescription()['fr']);
        $this->assertSame('Description EN', $theme->getDescription()['en']);
        $this->assertSame('000', $theme->getDefaultColors()['primary']);
        $this->assertSame('111', $theme->getDefaultColors()['secondary']);
        $this->assertSame('222', $theme->getDefaultColors()['third']);
        $this->assertSame('Roboto Slab', $theme->getDefaultFonts()['title']);
        $this->assertSame('Roboto', $theme->getDefaultFonts()['text']);
        $this->assertSame(10, $theme->getPostsPerPage());
        $this->assertSame(11, $theme->getEventsPerPage());
        $this->assertSame('style', trim($theme->getTemplates()['style']));
        $this->assertSame('', trim($theme->getTemplates()['script']));
        $this->assertSame('head', trim($theme->getTemplates()['head']));
        $this->assertSame('layout', trim($theme->getTemplates()['layout']));
        $this->assertSame('header', trim($theme->getTemplates()['header']));
        $this->assertSame('footer', trim($theme->getTemplates()['footer']));
        $this->assertSame('list', trim($theme->getTemplates()['list']));
        $this->assertSame('content', trim($theme->getTemplates()['content']));
        $this->assertSame('home', trim($theme->getTemplates()['home']));
        $this->assertSame('home-calls-to-action', trim($theme->getTemplates()['home-calls-to-action']));
        $this->assertSame('home-custom-content', trim($theme->getTemplates()['home-custom-content']));
        $this->assertSame('home-newsletter', trim($theme->getTemplates()['home-newsletter']));
        $this->assertSame('home-posts', trim($theme->getTemplates()['home-posts']));
        $this->assertSame('home-events', trim($theme->getTemplates()['home-events']));
        $this->assertSame('home-socials', trim($theme->getTemplates()['home-socials']));
        $this->assertSame('manifesto-list', trim($theme->getTemplates()['manifesto-list']));
        $this->assertSame('manifesto-view', trim($theme->getTemplates()['manifesto-view']));
        $this->assertSame('trombinoscope-list', trim($theme->getTemplates()['trombinoscope-list']));
        $this->assertSame('trombinoscope-view', trim($theme->getTemplates()['trombinoscope-view']));

        // Check thumbnail
        $this->assertInstanceOf(Upload::class, $theme->getThumbnail());

        $storage = static::getContainer()->get('cdn.storage');
        $pathname = $theme->getThumbnail()->getPathname();
        $this->assertTrue($storage->fileExists($pathname), $pathname.' should exist in storage.');

        $image = static::getContainer()->get(ImageManager::class)->read($storage->read($pathname));
        $this->assertSame(450, $image->width());
        $this->assertSame(400, $image->height());

        // Check asset
        $this->assertCount(1, $theme->getAssets());
        $this->assertInstanceOf(WebsiteThemeAsset::class, $asset = $theme->getAssets()[0]);
        $this->assertSame('assets/picture.jpg', $asset->getPathname());

        $storage = static::getContainer()->get('cdn.storage');
        $pathname = $asset->getFile()->getPathname();
        $this->assertTrue($storage->fileExists($pathname), $pathname.' should exist in storage.');
    }
}
