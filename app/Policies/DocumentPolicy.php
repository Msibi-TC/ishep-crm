<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $u, Document $d): bool
    {
        return $u->id === $d->owner_user_id || $u->hasPermission('memberships.review');
    }
}
