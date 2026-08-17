<?php

namespace App\Enums;

enum ApplicationQueryStatus: string
{
    case Open = 'open';
    case Responded = 'responded';
    case Resolved = 'resolved';
}
