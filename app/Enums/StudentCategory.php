<?php

namespace App\Enums;

enum StudentCategory: string
{
    case Grade12 = 'grade_12';
    case ActiveTertiary = 'active_tertiary';
    case ProspectiveTertiary = 'prospective_tertiary';
}
