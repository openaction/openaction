<?php

namespace App\Controller\Console\Api;

use App\Api\Transformer\Community\ContactPaymentListItemTransformer;
use App\Controller\Api\AbstractApiController;
use App\Repository\Community\ContactPaymentRepository;
use App\Repository\OrganizationMemberRepository;
use App\Repository\OrganizationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/api/{organizationUuid}/payments')]
class PaymentsController extends AbstractApiController
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
        private readonly OrganizationMemberRepository $memberRepository,
        private readonly ContactPaymentRepository $payments,
        private readonly ContactPaymentListItemTransformer $transformer,
    ) {
    }

    #[Route('', name: 'console_api_payments_list', methods: ['GET'])]
    public function list(string $organizationUuid, Request $request)
    {
        if (!$orga = $this->organizationRepository->findOneByUuid($organizationUuid)) {
            throw $this->createNotFoundException();
        }

        if (!$this->memberRepository->findMember($this->getUser(), $orga)) {
            throw $this->createNotFoundException();
        }

        $filters = [
            'type' => $request->query->get('type'),
            'method' => $request->query->get('method'),
            'status' => $request->query->get('status'),
            'amount_min' => $request->query->get('amount_min'),
            'amount_max' => $request->query->get('amount_max'),
            'date_min' => $request->query->get('date_min'),
            'date_max' => $request->query->get('date_max'),
        ];

        $page = $this->apiQueryParser->getPage();
        $limit = $this->apiQueryParser->getLimit() ?: 50;

        $paginator = $this->payments->createOrganizationPaymentsPaginator($orga, $filters, $page, $limit);

        return $this->handleApiCollection($paginator, $this->transformer, true);
    }
}
