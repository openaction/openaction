<?php

namespace App\Api\Persister;

use App\Api\Model\ContactPaymentApiData;
use App\Entity\Community\Contact;
use App\Entity\Community\ContactPayment;
use App\Entity\Community\Enum\ContactPaymentMethod;
use App\Entity\Community\Enum\ContactPaymentProvider;
use App\Entity\Community\Enum\ContactPaymentType;
use App\Entity\Community\Model\ContactPaymentMollieDetails;
use App\Entity\Project;
use App\Repository\Community\ContactPaymentRepository;
use App\Repository\Community\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;

class ContactPaymentApiPersister
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ContactRepository $contactRepository,
        private readonly ContactPaymentRepository $paymentRepository,
    ) {
    }

    public function persist(ContactPaymentApiData $data, Project $project): ContactPayment
    {
        $contact = $this->resolveContact($data, $project);

        $payment = new ContactPayment(
            $contact,
            ContactPaymentType::from($data->type),
            (int) $data->netAmount,
            (int) $data->feesAmount,
            strtoupper((string) $data->currency),
            ContactPaymentProvider::from($data->paymentProvider),
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

        // Membership period auto calculation
        if (ContactPaymentType::Membership === $payment->getType()) {
            $start = $this->computeMembershipStart($contact);
            $end = $start->modify('+1 year');
            $payment->setMembershipPeriod($start, $end);
        }

        // Metadata
        $payment->setMetadata($data->metadata ?: null);

        // Provider-specific details
        if ($data->paymentProvider === ContactPaymentProvider::Mollie->value) {
            $details = $data->paymentProviderDetails ?? [];
            $transactionId = (string) ($details['transactionId'] ?? '');
            $rawPayload = (array) ($details['rawPayload'] ?? []);
            $payment->setPaymentProviderDetails(new ContactPaymentMollieDetails($transactionId, $rawPayload));
        }

        $this->em->persist($payment);
        $this->em->flush();

        return $payment;
    }

    private function resolveContact(ContactPaymentApiData $data, Project $project): Contact
    {
        if ($data->contactId) {
            $contact = $this->contactRepository->findOneByBase62Uid($data->contactId);
        } elseif ($data->email) {
            $contact = $this->contactRepository->findOneByAnyEmail($project->getOrganization(), $data->email);
        } else {
            throw new \InvalidArgumentException('You must provide either contactId or email');
        }

        if (!$contact) {
            throw new \RuntimeException('Contact not found');
        }

        return $contact;
    }

    private function computeMembershipStart(Contact $contact): \DateTimeImmutable
    {
        $today = new \DateTimeImmutable('today');

        // Find an active membership (today between start and end)
        $qb = $this->paymentRepository->createQueryBuilder('p')
            ->andWhere('p.contact = :contact')
            ->setParameter('contact', $contact)
            ->andWhere('p.type = :type')
            ->setParameter('type', ContactPaymentType::Membership)
            ->andWhere('p.membershipStartAt <= :now')
            ->andWhere('p.membershipEndAt >= :now')
            ->setParameter('now', $today)
            ->orderBy('p.membershipEndAt', 'DESC')
            ->setMaxResults(1);

        $active = $qb->getQuery()->getOneOrNullResult();

        if ($active) {
            /* @var ContactPayment $active */
            return $active->getMembershipEndAt()?->modify('+1 day') ?? $today;
        }

        return $today;
    }
}
