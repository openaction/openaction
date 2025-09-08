<?php

namespace App\Entity\Community\Enum;

enum ContactPaymentMethod: string
{
    case Card = 'Card';
    case Wire = 'Wire';
    case Check = 'Check';
    case Cash = 'Cash';
    case Sepa = 'Sepa';
    case Other = 'Other';
}
