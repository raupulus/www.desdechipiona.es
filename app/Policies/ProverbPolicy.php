<?php

namespace App\Policies;

use App\Models\Proverb;
use App\Models\User;

/**
 * Policy de autorización para el modelo Proverb.
 */
class ProverbPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Proverb $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Proverb $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Proverb $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
