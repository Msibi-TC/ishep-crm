<?php

namespace App\Enums;

enum BillingPeriod: string
{
    case Monthly = 'monthly';
    case Annual = 'annual';
    case OnceOff = 'once_off';
}
