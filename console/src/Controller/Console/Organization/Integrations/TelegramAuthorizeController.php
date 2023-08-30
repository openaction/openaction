<?php

namespace App\Controller\Console\Organization\Integrations;

use App\Controller\AbstractController;
use App\Entity\Integration\TelegramApp;
use App\Entity\Integration\TelegramAppAuthorization;
use App\Repository\Integration\TelegramAppAuthorizationRepository;
use App\Repository\Integration\TelegramAppRepository;
use App\Repository\OrganizationMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramAuthorizeController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private TelegramAppRepository $repository,
        private OrganizationMemberRepository $memberRepository,
    ) {
    }

    #[Route('/console/i/t-me/{encodedUuid}', name: 'console_organization_integrations_telegram_authorize')]
    public function authorize(TelegramAppAuthorizationRepository $authorizationRepository, string $encodedUuid)
    {
        if (!$app = $this->repository->findOneByBase62Uid($encodedUuid)) {
            throw $this->createNotFoundException();
        }

        if (!$this->memberRepository->findMember($this->getUser(), $app->getOrganization())) {
            throw $this->createNotFoundException();
        }

        if ($authorization = $authorizationRepository->findAuthorization($app, $this->getUser())) {
            return $this->redirect('https://telegram.me/'.$app->getBotUsername().'?start='.$authorization->getApiToken());
        }

        return $this->render('console/organization/integrations/telegram/authorize.html.twig', [
            'telegramApp' => $app,
            'pictureUrl' => $this->fetchBotPictureUrl($app->getBotUsername()),
        ]);
    }

    #[Route('/console/i/t-me/{uuid}/accept', name: 'console_organization_integrations_telegram_accept')]
    public function accept(EntityManagerInterface $em, Request $request, TelegramApp $app)
    {
        $this->denyUnlessValidCsrf($request);

        if (!$this->memberRepository->findMember($this->getUser(), $app->getOrganization())) {
            throw $this->createNotFoundException();
        }

        $authorization = new TelegramAppAuthorization($app, $this->getUser());

        $em->persist($authorization);
        $em->flush();

        return $this->redirect('https://telegram.me/'.$app->getBotUsername().'?start='.$authorization->getApiToken());
    }

    private function fetchBotPictureUrl(string $username): ?string
    {
        $response = $this->httpClient->request('GET', 'https://telegram.me/'.$username);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        $crawler = new Crawler($response->getContent());

        try {
            return $crawler->filter('[property="og:image"]')->first()->attr('content') ?: null;
        } catch (\Exception) {
            return null;
        }
    }
}
