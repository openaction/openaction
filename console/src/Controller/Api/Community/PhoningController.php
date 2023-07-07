<?php

namespace App\Controller\Api\Community;

use App\Api\Model\PhoningCampaignCallApiData;
use App\Api\Persister\PhoningCampaignCallApiPersister;
use App\Api\Transformer\Community\PhoningCampaignCallTransformer;
use App\Api\Transformer\Community\PhoningCampaignTransformer;
use App\Community\Member\AuthorizationToken;
use App\Community\MemberAuthenticator;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Community\Contact;
use App\Entity\Community\PhoningCampaign;
use App\Entity\Community\PhoningCampaignCall;
use App\Repository\Community\PhoningCampaignCallRepository;
use App\Repository\Community\PhoningCampaignRepository;
use App\Repository\Community\PhoningCampaignTargetRepository;
use App\Util\Json;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Phone campaigns')]
#[Route('/api/community/area/phoning')]
class PhoningController extends AbstractApiController
{
    use ApiControllerTrait;

    private MemberAuthenticator $authenticator;
    private PhoningCampaignRepository $campaignRepository;
    private PhoningCampaignTargetRepository $targetRepository;
    private PhoningCampaignCallRepository $callRepository;
    private PhoningCampaignCallApiPersister $callPersister;

    public function __construct(
        MemberAuthenticator $authenticator,
        PhoningCampaignRepository $campaignRepository,
        PhoningCampaignTargetRepository $targetRepository,
        PhoningCampaignCallRepository $callRepository,
        PhoningCampaignCallApiPersister $callPersister
    ) {
        $this->authenticator = $authenticator;
        $this->campaignRepository = $campaignRepository;
        $this->targetRepository = $targetRepository;
        $this->callRepository = $callRepository;
        $this->callPersister = $callPersister;
    }

    #[Route('/{encodedUuid}', name: 'api_community_phoning_campaign_view', methods: ['GET'])]
    public function viewCampaign(PhoningCampaignTransformer $transformer, Request $request, string $encodedUuid)
    {
        $this->denyUnlessAuthorized($request);
        $campaign = $this->findActiveCampaignOrThrow($encodedUuid);
        $this->denyUnlessSameProject($campaign);

        return $this->handleApiItem($campaign, $transformer);
    }

    #[Route('/{encodedUuid}/resolve-target', name: 'api_website_campaign_resolve_target', methods: ['POST'])]
    public function resolveTarget(PhoningCampaignCallTransformer $transformer, Request $request, string $encodedUuid)
    {
        $author = $this->denyUnlessAuthorized($request);
        $campaign = $this->findActiveCampaignOrThrow($encodedUuid);
        $this->denyUnlessSameProject($campaign);

        if (!$target = $this->targetRepository->findPhoningTarget($author, $campaign)) {
            throw $this->createNotFoundException('No target found');
        }

        $call = $this->callPersister->startCall($target, $author);

        return $this->handleApiItem($call, $transformer);
    }

    #[Route('/{encodedUuid}/call/{callId}', name: 'api_website_campaign_call_view', methods: ['GET'])]
    public function viewCall(PhoningCampaignCallTransformer $transformer, Request $request, string $encodedUuid, string $callId)
    {
        $this->denyUnlessAuthorized($request);
        $campaign = $this->findActiveCampaignOrThrow($encodedUuid);
        $this->denyUnlessSameProject($campaign);

        /** @var PhoningCampaignCall $call */
        if (!$call = $this->callRepository->findOneByBase62Uid($callId)) {
            throw $this->createNotFoundException('Call not found');
        }

        if ($call->getTarget()->getCampaign()->getId() !== $campaign->getId()) {
            throw $this->createNotFoundException('Call target is not linked to the campaign');
        }

        return $this->handleApiItem($call, $transformer);
    }

    #[Route('/{encodedUuid}/call/{callId}/save', name: 'api_website_campaign_target_save_call', methods: ['POST'])]
    public function saveCall(ValidatorInterface $validator, PhoningCampaignCallTransformer $transformer, Request $request, string $encodedUuid, string $callId)
    {
        $this->denyUnlessAuthorized($request);
        $campaign = $this->findActiveCampaignOrThrow($encodedUuid);
        $this->denyUnlessSameProject($campaign);

        /** @var PhoningCampaignCall $call */
        if (!$call = $this->callRepository->findOneByBase62Uid($callId)) {
            throw $this->createNotFoundException('Call not found');
        }

        if ($call->getTarget()->getCampaign()->getId() !== $campaign->getId()) {
            throw $this->createNotFoundException('Call target is not linked to the campaign');
        }

        try {
            $payload = Json::decode($request->getContent());
        } catch (\JsonException) {
            return $this->createJsonApiProblemResponse('Invalid JSON provided as payload', Response::HTTP_BAD_REQUEST);
        }

        $data = PhoningCampaignCallApiData::createFromPayload($payload);

        $errors = $validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        $this->callPersister->saveCall($call, $data);

        return $this->handleApiItem($call, $transformer);
    }

    private function findActiveCampaignOrThrow(string $encodedUuid): PhoningCampaign
    {
        if (!$campaign = $this->campaignRepository->findOneByBase62Uid($encodedUuid)) {
            throw $this->createNotFoundException('Campaign not found');
        }

        if (!$campaign->isActive()) {
            throw $this->createNotFoundException('Inactive campaign');
        }

        return $campaign;
    }

    private function denyUnlessAuthorized(Request $request): Contact
    {
        try {
            $token = AuthorizationToken::createFromPayload(
                Json::decode($request->headers->get(MemberAuthenticator::TOKEN_HEADER)) ?? []
            );
        } catch (\Exception) {
            throw $this->createNotFoundException('Not authorized');
        }

        if (!$contact = $this->authenticator->authorize($token)) {
            throw $this->createNotFoundException('Not authorized');
        }

        return $contact;
    }
}
