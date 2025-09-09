<?php

namespace App\Entity\Community\Enum;

enum ContactPaymentType: string
{
    case Donation = 'Donation';
    case Membership = 'Membership';
    case ElectedOfficialContribution = 'ElectedOfficialContribution';
}
