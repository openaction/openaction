<?php

namespace App\Controller\Console\Api;

use App\Api\Model\ContactPaymentApiData;
use App\Api\Model\ContactPaymentScheduleApiData;
use App\Api\Transformer\Community\ContactPaymentListItemTransformer;
use App\Controller\Api\AbstractApiController;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactPayment;
use App\Entity\Community\ContactSubscription;
use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Repository\Community\ContactPaymentRepository;
use App\Repository\Community\ContactRepository;
use App\Repository\Community\ContactSubscriptionRepository;
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
        private readonly ContactSubscriptionRepository $subscriptions,
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

        $contact = $this->resolveOrganizationContact($orga, $data->contactId, $data->email);
        if (!$contact) {
            return $this->createJsonApiProblemResponse('Contact not found in organization', 404);
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
            $this->parseBirthdate($data->birthdate),
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

    #[Route('/schedule', name: 'console_api_payments_schedule', methods: ['POST'])]
    public function schedulePayment(string $organizationUuid, Request $request)
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

        $data = ContactPaymentScheduleApiData::createFromPayload($payload);
        $errors = $this->validator->validate($data);
        if ($errors->count() > 0) {
            return $this->handleApiConstraintViolations($errors);
        }

        if (ContactPaymentMethod::Sepa->value !== $data->paymentMethod) {
            return $this->createJsonApiProblemResponse('Only Sepa payment method is supported for payment schedules.', 400);
        }

        $contact = $this->resolveOrganizationContact($orga, $data->contactId, $data->email);
        if (!$contact) {
            return $this->createJsonApiProblemResponse('Contact not found in organization', 404);
        }

        $startDate = $this->parseDate($data->startDate);
        if (!$startDate) {
            return $this->createJsonApiProblemResponse('Invalid startDate provided.', 400);
        }

        $endsAt = $this->computeScheduleEnd($startDate, (int) $data->intervalInMonths, $data->occurrences, $data->endDate);
        if (false === $endsAt) {
            return $this->createJsonApiProblemResponse('Invalid schedule horizon provided.', 400);
        }

        $type = ContactPaymentType::from($data->type);
        $paymentMethod = ContactPaymentMethod::from($data->paymentMethod);
        $subscription = $this->subscriptions->findActiveByContactTypeMethod($contact, $type, $paymentMethod);

        if ($subscription) {
            $subscription->updateSchedule(
                (int) $data->netAmount,
                (int) $data->feesAmount,
                strtoupper((string) $data->currency),
                $paymentMethod,
                (int) $data->intervalInMonths,
                $startDate,
                $endsAt,
            );
        } else {
            $subscription = new ContactSubscription(
                $contact,
                $type,
                (int) $data->netAmount,
                (int) $data->feesAmount,
                strtoupper((string) $data->currency),
                $paymentMethod,
                (int) $data->intervalInMonths,
                $startDate,
                $endsAt,
            );
        }

        $subscription->setPayerSnapshot(
            $data->civility,
            $data->firstName,
            $data->lastName,
            $data->payerEmail,
            $data->streetAddressLine1,
            $data->streetAddressLine2,
            $data->city,
            $data->postalCode,
            $data->countryCode,
            $this->parseBirthdate($data->birthdate),
            $data->phone,
            $data->nationality,
            $data->fiscalCountryCode,
        );
        $subscription->setMetadata($data->metadata ?: null);

        $this->em->persist($subscription);

        $created = [];
        if (null === $subscription->getId() || !$this->payments->existsForSubscriptionAndDate($subscription, $startDate)) {
            $payment = $subscription->createPaymentForDate($startDate);
            $this->em->persist($payment);
            $created[] = $payment;
        }

        $this->em->flush();

        return $this->handleApiCollection($created, $this->transformer, false);
    }

    private function resolveOrganizationContact(
        \App\Entity\Organization $organization,
        ?string $contactId,
        ?string $email,
    ): ?Contact {
        if ($contactId) {
            $contact = $this->contacts->findOneByBase62Uid($contactId);

            return $contact && $contact->getOrganization()->getId() === $organization->getId() ? $contact : null;
        }

        if ($email) {
            return $this->contacts->findOneByAnyEmail($organization, $email);
        }

        return null;
    }

    private function parseBirthdate(?string $birthdate): ?\DateTime
    {
        if (!$birthdate) {
            return null;
        }

        try {
            return new \DateTime($birthdate);
        } catch (\Exception) {
            return null;
        }
    }

    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        if (!$value) {
            return null;
        }

        try {
            return (new \DateTimeImmutable($value))->setTime(0, 0, 0);
        } catch (\Exception) {
            return null;
        }
    }

    private function computeScheduleEnd(
        \DateTimeImmutable $startDate,
        int $intervalInMonths,
        ?int $occurrences,
        ?string $endDate,
    ): \DateTimeImmutable|false|null {
        $endFromOccurrences = null;
        if (null !== $occurrences) {
            if ($occurrences < 1) {
                return false;
            }

            $months = ($occurrences - 1) * $intervalInMonths;
            $endFromOccurrences = $startDate->modify(sprintf('+%d months', $months));
        }

        $endFromDate = $this->parseDate($endDate);
        if ($endDate && !$endFromDate) {
            return false;
        }

        $endsAt = $endFromOccurrences;
        if ($endFromDate && (!$endsAt || $endFromDate < $endsAt)) {
            $endsAt = $endFromDate;
        }

        if ($endsAt && $endsAt < $startDate) {
            return false;
        }

        return $endsAt;
    }
}
