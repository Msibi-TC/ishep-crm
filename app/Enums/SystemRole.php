<?php

namespace App\Enums;

enum SystemRole: string
{
    case RegisteredUser = 'registered_user';
    case Administrator = 'administrator';
    case Finance = 'finance';
    case SuperUser = 'super_user';
}
