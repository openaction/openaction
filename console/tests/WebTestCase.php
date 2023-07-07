<?php

namespace App\Tests;

use App\Repository\UserRepository;
use App\Util\Json;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Webmozart\Assert\Assert;

abstract class WebTestCase extends BaseWebTestCase
{
    public const USER_TGALOPIN_UUID = '17ef21d1-27ad-46a6-a2fe-95e5e725473c';
    public const USER_TGALOPIN_EMAIL = 'titouan.galopin@citipo.com';

    public const PROJECT_CITIPO_UUID = 'e816bcc6-0568-46d1-b0c5-917ce4810a87';
    public const PROJECT_IDF_UUID = '151f1340-9ad6-47c7-a8a5-838ff955eae7';
    public const PROJECT_ACME_UUID = '2c720420-65fd-4360-9d77-731758008497';
    public const PROJECT_EXAMPLECO_UUID = '643e47ea-fd9d-4963-958f-05970de2f88b';

    public const ORGA_CITIPO_UUID = '219025aa-7fe2-4385-ad8f-31f386720d10';
    public const ORGA_ACME_UUID = 'cbeb774c-284c-43e3-923a-5a2388340f91';

    protected function authenticate(KernelBrowser $browser, string $email = self::USER_TGALOPIN_EMAIL)
    {
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        Assert::notNull($user, 'User not found for email '.$email);

        $browser->loginUser($user);
    }

    protected function filterGlobalCsrfToken(Crawler $crawler): ?string
    {
        return Json::decode($crawler->filter('#exposed-data')->text())['token'] ?? null;
    }

    protected function logout(KernelBrowser $browser)
    {
        $browser->getCookieJar()->clear();
    }

    protected function assertApiResponse(array $result, array $expectedValues, string $path = '')
    {
        foreach ($expectedValues as $key => $expectedValue) {
            $this->assertArrayHasKey($key, $result);

            // Assert arrays recursively
            if (is_array($expectedValue)) {
                $this->assertApiResponse($result[$key], $expectedValue, $path.$key.'.');

                continue;
            }

            $this->assertSame($expectedValue, $result[$key], 'Key '.$path.$key);
        }
    }
}
