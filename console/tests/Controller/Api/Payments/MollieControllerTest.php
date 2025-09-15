<?php

namespace App\Tests\Controller\Api\Payments;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Tests\ApiTestCase;

class MollieControllerTest extends ApiTestCase
{
    public function testReturnsExistingToken(): void
    {
        $client = self::createClient();

        // Ensure organization has a valid access token for > 5 minutes
        /** @var OrganizationRepository $orgas */
        $orgas = static::getContainer()->get(OrganizationRepository::class);
        /** @var Organization $orga */
        $orga = $orgas->findOneBy(['name' => 'Example Co']);
        $orga->setMollieConnectAccessToken('existing_access');
        $orga->setMollieConnectRefreshToken('existing_refresh');
        $orga->setMollieConnectAccessTokenExpiresAt((new \DateTimeImmutable('now'))->modify('+30 minutes'));

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($orga);
        $em->flush();

        $result = $this->apiRequest(
            $client,
            'POST',
            '/api/payments/mollie/token',
            self::EXAMPLECO_TOKEN
        );

        $this->assertSame('MollieAccessToken', $result['_resource']);
        $this->assertSame('existing_access', $result['accessToken']);
        $this->assertNotEmpty($result['expiresAt']);
    }

    public function testRefreshesTokenWhenExpiringSoon(): void
    {
        $client = self::createClient();

        /** @var OrganizationRepository $orgas */
        $orgas = static::getContainer()->get(OrganizationRepository::class);
        /** @var Organization $orga */
        $orga = $orgas->findOneBy(['name' => 'Example Co']);
        $orga->setMollieConnectAccessToken('old_access');
        $orga->setMollieConnectRefreshToken('old_refresh');
        // Expire in < 5 minutes
        $orga->setMollieConnectAccessTokenExpiresAt((new \DateTimeImmutable('now'))->modify('+2 minutes'));

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($orga);
        $em->flush();

        $result = $this->apiRequest(
            $client,
            'POST',
            '/api/payments/mollie/token',
            self::EXAMPLECO_TOKEN
        );

        $this->assertSame('MollieAccessToken', $result['_resource']);
        $this->assertSame('refreshed_access', $result['accessToken']);
        $this->assertNotEmpty($result['expiresAt']);

        // Check DB updated
        $em->clear();
        /** @var Organization $reloaded */
        $reloaded = $orgas->findOneBy(['name' => 'Example Co']);
        $this->assertSame('refreshed_access', $reloaded->getMollieConnectAccessToken());
        $this->assertSame('refreshed_refresh', $reloaded->getMollieConnectRefreshToken());
        $this->assertNotNull($reloaded->getMollieConnectAccessTokenExpiresAt());
    }
}

