<?php

namespace App\Controller\Console\Api;

use App\Api\Model\ContactPaymentApiData;
use App\Api\Transformer\Community\ContactPaymentListItemTransformer;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Community\ContactPayment;
use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Repository\Community\ContactPaymentRepository;
use App\Repository\Community\ContactRepository;
use App\Repository\OrganizationMemberRepository;
use App\Repository\OrganizationRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/console/api/{organizationUuid}/payments')]
class PaymentsController extends AbstractApiController
{
    use ApiControllerTrait;

    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
        private readonly OrganizationMemberRepository $memberRepository,
        private readonly ContactPaymentRepository $payments,
        private readonly ContactPaymentListItemTransformer $transformer,
        private readonly EntityManagerInterface $em,
        private readonly ContactRepository $contacts,
        private readonly ValidatorInterface $validator,
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

    #[Route('', name: 'console_api_payments_add', methods: ['POST'])]
    public function addPayment(string $organizationUuid, Request $request)
    {
        if (!$orga = $this->organizationRepository->findOneByUuid($organizationUuid)) {
            throw $this->createNotFoundException();
        }

        if (!$this->memberRepository->findMember($this->getUser(), $orga)) {
            throw $this->createNotFoundException();
        }

        try {
            $payload = Json::decode($request->getContent());
        } catch (\JsonException) {
            return $this->createJsonApiProblemResponse('Invalid JSON provided as payload', 400);
        }

        // Reuse API validation model
        $data = ContactPaymentApiData::createFromPayload($payload);
        $errors = $this->validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        // Only Manual provider supported in console for now
        if ($data->paymentProvider !== ContactPaymentProvider::Manual->value) {
            return $this->createJsonApiProblemResponse('Only Manual provider is supported for console payments currently.', 400);
        }

        // Resolve contact by id or email in orga
        if ($data->contactId) {
            $contact = $this->contacts->findOneByBase62Uid($data->contactId);
            if (!$contact || $contact->getOrganization()->getId() !== $orga->getId()) {
                return $this->createJsonApiProblemResponse('Contact not found in organization', 404);
            }
        } elseif ($data->email) {
            $contact = $this->contacts->findOneByAnyEmail($orga, $data->email);
            if (!$contact) {
                return $this->createJsonApiProblemResponse('Contact not found in organization', 404);
            }
        } else {
            return $this->createJsonApiProblemResponse('You must provide either contactId or email', 400);
        }

        // Create payment entity
        $payment = new ContactPayment(
            $contact,
            ContactPaymentType::from($data->type),
            (int) $data->netAmount,
            (int) $data->feesAmount,
            strtoupper((string) $data->currency),
            ContactPaymentProvider::Manual,
            ContactPaymentMethod::from($data->paymentMethod),
        );

        // Payer snapshot
        $birthdate = null;
        if ($data->birthdate) {
            try {
                $birthdate = new \DateTime((string) $data->birthdate);
            } catch (\Exception) {
                $birthdate = null;
            }
        }
        $payment->setPayerSnapshot(
            $data->civility,
            $data->firstName,
            $data->lastName,
            $data->payerEmail,
            $data->streetAddressLine1,
            $data->streetAddressLine2,
            $data->city,
            $data->postalCode,
            $data->countryCode,
            $birthdate,
            $data->phone,
            $data->nationality,
            $data->fiscalCountryCode,
        );

        // Membership period calculation for membership payments
        if (ContactPaymentType::Membership === $payment->getType()) {
            $today = new \DateTimeImmutable('today');

            $qb = $this->payments->createQueryBuilder('p')
                ->andWhere('p.contact = :contact')
                ->setParameter('contact', $contact)
                ->andWhere('p.type = :type')
                ->setParameter('type', ContactPaymentType::Membership)
                ->andWhere('p.membershipStartAt <= :now')
                ->andWhere('p.membershipEndAt >= :now')
                ->setParameter('now', $today)
                ->orderBy('p.membershipEndAt', 'DESC')
                ->setMaxResults(1)
            ;

            $active = $qb->getQuery()->getOneOrNullResult();
            if ($active instanceof ContactPayment && $active->getMembershipEndAt()) {
                $start = $active->getMembershipEndAt()->modify('+1 day');
            } else {
                $start = $today;
            }
            $end = $start->modify('+1 year');
            $payment->setMembershipPeriod($start, $end);
        }

        // Optional metadata
        $payment->setMetadata($data->metadata ?: null);

        $this->em->persist($payment);
        $this->em->flush();

        return $this->handleApiItem($payment, $this->transformer);
    }
}
