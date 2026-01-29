<?php

namespace App\Tests\Bridge\Github;

use App\Bridge\Github\Github;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubTest extends TestCase
{
    private const FIXTURE_KEY_PATH = __DIR__.'/../../Fixtures/keys/github_app_private_key.pem';

    public function testBase64EncodedKeyIsDecoded(): void
    {
        $pem = file_get_contents(self::FIXTURE_KEY_PATH);

        $this->assertNotFalse($pem);

        $encoded = base64_encode($pem);
        $github = new Github(
            $this->createMock(HttpClientInterface::class),
            $this->createMock(CacheInterface::class),
            '123',
            $encoded
        );

        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())
            ->method('expiresAfter')
            ->with(5 * 60)
            ->willReturn($item);

        $method = new \ReflectionMethod(Github::class, 'createJwt');
        $method->setAccessible(true);

        $jwt = $method->invoke($github, $item);

        $this->assertIsString($jwt);
        $this->assertSame(2, substr_count($jwt, '.'));
    }
}
