<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        $userRole = $user->getRoleNames()->first();
        $postUserRole = $post->author->getRoleNames()->first();

        return $user->id === $post->user_id ||
            $this->roleLevel($userRole) > $this->roleLevel($postUserRole);
    }


    /**
     * Helper to define role hierarchy
     */
    protected function roleLevel($role)
    {
        $levels = [
            'admin' => 3,
            'editor' => 2,
            'author' => 1,
        ];

        return $levels[$role] ?? 0;
    }
}