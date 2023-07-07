<?php

namespace App\Controller\Api\Integrations;

use App\Api\Transformer\Integrations\OrganizationMemberTransformer;
use App\Controller\Api\AbstractApiController;
use App\Entity\Integration\TelegramAppAuthorization;
use App\Repository\OrganizationMemberRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Integrations')]
#[Route('/api/integrations/account')]
class AccountController extends AbstractApiController
{
    /**
     * Get the currently authenticated collaborator details.
     */
    #[Route('', name: 'api_integrations_account', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return the current collaborator details.',
        content: new OA\JsonContent(ref: '#/components/schemas/OrganizationMember')
    )]
    public function account(OrganizationMemberRepository $memberRepository, OrganizationMemberTransformer $transformer)
    {
        /** @var TelegramAppAuthorization $authorization */
        $authorization = $this->getUser();

        if (!$member = $memberRepository->findMember($authorization->getMember(), $authorization->getApp()->getOrganization())) {
            throw $this->createNotFoundException('Member not a part of the app organization');
        }

        return $this->handleApiItem($member, $transformer);
    }
}
