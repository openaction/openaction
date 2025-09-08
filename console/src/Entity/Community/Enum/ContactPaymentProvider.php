<?php

namespace App\Entity\Community\Enum;

enum ContactPaymentProvider: string
{
    case Mollie = 'Mollie';
    case Manual = 'Manual';
}
