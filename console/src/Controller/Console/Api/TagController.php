<?php

namespace App\Controller\Console\Api;

use App\Controller\AbstractController;
use App\Repository\Community\TagRepository;
use App\Repository\OrganizationMemberRepository;
use App\Repository\OrganizationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/api/tags')]
class TagController extends AbstractController
{
    public function __construct(
        private TagRepository $tagRepository,
        private OrganizationRepository $organizationRepository,
        private OrganizationMemberRepository $memberRepository,
    ) {
    }

    #[Route('/search', name: 'console_api_tags_search', methods: ['GET'])]
    public function search(Request $request)
    {
        if (!$request->query->has('o')) {
            throw $this->createNotFoundException();
        }

        if (!$orga = $this->organizationRepository->findOneByUuid($request->query->get('o'))) {
            throw $this->createNotFoundException();
        }

        if (!$this->memberRepository->findMember($this->getUser(), $orga)) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse($this->tagRepository->search($orga, $request->query->get('q')));
    }
}
