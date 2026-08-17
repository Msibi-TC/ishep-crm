<?php

namespace App\Enums;

enum OrganizationStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Inactive = 'inactive';
}
