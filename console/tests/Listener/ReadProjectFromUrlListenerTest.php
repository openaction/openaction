<?php

namespace App\Tests\Listener;

use App\Entity\Organization;
use App\Entity\Project;
use App\Listener\ReadProjectFromUrlListener;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ReadProjectFromUrlListenerTest extends KernelTestCase
{
    private UserRepository $userRepository;
    private TokenStorageInterface $tokenStorage;
    private ReadProjectFromUrlListener $listener;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->listener = static::getContainer()->get(ReadProjectFromUrlListener::class);
        $this->tokenStorage = static::getContainer()->get(TokenStorageInterface::class);
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
    }

    public function testValidOrganizationUuid()
    {
        $user = $this->userRepository->findOneByEmail('titouan.galopin@citipo.com');
        $this->tokenStorage->setToken(new UsernamePasswordToken($user, 'main'));

        $this->listener->onKernelRequest($this->createRequestEvent(
            $request = new Request(),
            null,
            '219025aa-7fe2-4385-ad8f-31f386720d10'
        ));

        $this->assertNull($request->attributes->get('project'));

        $this->assertInstanceOf(Organization::class, $orga = $request->attributes->get('organization'));
        $this->assertSame('219025aa-7fe2-4385-ad8f-31f386720d10', $orga->getUuid()->toRfc4122());
    }

    public function testValidProjectUuid()
    {
        $user = $this->userRepository->findOneByEmail('titouan.galopin@citipo.com');
        $this->tokenStorage->setToken(new UsernamePasswordToken($user, 'main'));

        $this->listener->onKernelRequest($this->createRequestEvent(
            $request = new Request(),
            'e816bcc6-0568-46d1-b0c5-917ce4810a87',
            null
        ));

        $this->assertInstanceOf(Project::class, $project = $request->attributes->get('project'));
        $this->assertSame('e816bcc6-0568-46d1-b0c5-917ce4810a87', $project->getUuid()->toRfc4122());

        $this->assertInstanceOf(Organization::class, $orga = $request->attributes->get('organization'));
        $this->assertSame('219025aa-7fe2-4385-ad8f-31f386720d10', $orga->getUuid()->toRfc4122());
    }

    public function testAnonymousOrganizationFails()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->listener->onKernelRequest($this->createRequestEvent(
            $request = new Request(),
            null,
            '219025aa-7fe2-4385-ad8f-31f386720d10'
        ));

        $this->assertNull($request->attributes->get('project'));
        $this->assertNull($request->attributes->get('organization'));
    }

    public function testAnonymousProjectUuidFails()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->listener->onKernelRequest($this->createRequestEvent(
            $request = new Request(),
            'e816bcc6-0568-46d1-b0c5-917ce4810a87',
            null
        ));

        $this->assertNull($request->attributes->get('project'));
        $this->assertNull($request->attributes->get('organization'));
    }

    public function testNoUuid()
    {
        $this->listener->onKernelRequest($this->createRequestEvent($request = new Request(), null, null));

        $this->assertNull($request->attributes->get('project'));
        $this->assertNull($request->attributes->get('organization'));
    }

    public function testInvalidOrganizationUuid()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->listener->onKernelRequest($this->createRequestEvent(
            $request = new Request(),
            null,
            'fcee131a-b9d5-45a6-b254-ad5b6045313a'
        ));

        $this->assertNull($request->attributes->get('project'));
        $this->assertNull($request->attributes->get('organization'));
    }

    public function testInvalidProjectUuid()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->listener->onKernelRequest($this->createRequestEvent(
            $request = new Request(),
            'fcee131a-b9d5-45a6-b254-ad5b6045313a',
            null
        ));

        $this->assertNull($request->attributes->get('project'));
        $this->assertNull($request->attributes->get('organization'));
    }

    private function createRequestEvent(Request $request, ?string $projectUuid, ?string $organizationUuid): RequestEvent
    {
        if ($projectUuid) {
            $request->attributes->set('projectUuid', $projectUuid);
        }

        if ($organizationUuid) {
            $request->attributes->set('organizationUuid', $organizationUuid);
        }

        return new RequestEvent(self::$kernel, $request, HttpKernelInterface::MAIN_REQUEST);
    }
}
