<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

/**
 * Policy de autorización para el modelo News.
 * Solo administradores pueden gestionar noticias en el panel.
 */
class NewsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, News $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, News $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, News $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, News $model): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, News $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
