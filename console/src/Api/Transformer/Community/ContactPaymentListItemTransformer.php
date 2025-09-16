<?php

namespace App\Api\Transformer\Community;

use App\Api\Transformer\AbstractTransformer;
use App\Entity\Community\ContactPayment;
use App\Util\Uid;

class ContactPaymentListItemTransformer extends AbstractTransformer
{
    public function transform(ContactPayment $payment): array
    {
        $contact = $payment->getContact();
        $recruitedBy = $contact->getRecruitedBy();

        // Compute status
        $status = 'pending';
        if ($payment->getRefundedAt()) {
            $status = 'refunded';
        } elseif ($payment->getCanceledAt()) {
            $status = 'canceled';
        } elseif ($payment->getCapturedAt()) {
            $status = 'captured';
        } elseif ($payment->getFailedAt()) {
            $status = 'failed';
        }

        $organizationUuid = $contact->getOrganization()->getUuid()->toRfc4122();

        return [
            '_resource' => 'ContactPayment',
            'id' => (int) $payment->getId(),
            'type' => $payment->getType()->value,
            'method' => $payment->getPaymentMethod()->value,
            'provider' => $payment->getPaymentProvider()->value,
            'status' => $status,
            'netAmount' => (int) $payment->getNetAmount(),
            'feesAmount' => (int) $payment->getFeesAmount(),
            'totalAmount' => (int) ($payment->getNetAmount() + $payment->getFeesAmount()),
            'currency' => $payment->getCurrency(),
            'date' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
            'membershipEndAt' => $payment->getMembershipEndAt()?->format('Y-m-d H:i:s'),
            'contact' => [
                'id' => Uid::toBase62($contact->getUuid()),
                'email' => $contact->getEmail(),
                'fullName' => $contact->getFullName(),
                'profileUrl' => $this->createLink('console_organization_community_contacts_view', [
                    'organizationUuid' => $organizationUuid,
                    'uuid' => $contact->getUuid()->toRfc4122(),
                ]),
            ],
            'recruitedBy' => $recruitedBy ? [
                'id' => Uid::toBase62($recruitedBy->getUuid()),
                'email' => $recruitedBy->getEmail(),
                'fullName' => $recruitedBy->getFullName(),
                'profileUrl' => $this->createLink('console_organization_community_contacts_view', [
                    'organizationUuid' => $organizationUuid,
                    'uuid' => $recruitedBy->getUuid()->toRfc4122(),
                ]),
            ] : null,
        ];
    }
}
