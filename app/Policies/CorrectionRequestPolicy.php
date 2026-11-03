<?php

namespace App\Policies;

use App\Models\CorrectionRequest;
use App\Models\User;

/**
 * Policy de autorización para el modelo CorrectionRequest.
 */
class CorrectionRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, CorrectionRequest $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, CorrectionRequest $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, CorrectionRequest $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
