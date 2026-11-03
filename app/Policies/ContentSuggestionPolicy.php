<?php

namespace App\Policies;

use App\Models\ContentSuggestion;
use App\Models\User;

/**
 * Policy de autorización para el modelo ContentSuggestion.
 */
class ContentSuggestionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ContentSuggestion $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ContentSuggestion $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ContentSuggestion $model): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
