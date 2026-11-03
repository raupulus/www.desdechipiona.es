<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

/**
 * Policy de autorización para el modelo Comment.
 */
class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Comment $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Comment $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Comment $model): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
