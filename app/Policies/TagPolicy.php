<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $user->id === $tag->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tag $tag): bool
    {
        $userRole = $user->getRoleNames()->first();
        $tagUserRole = $tag->user->getRoleNames()->first();

        return $user->id === $tag->user_id ||
            $this->roleLevel($userRole) > $this->roleLevel($tagUserRole);
    }


    /**
     * Helper to define role hierarchy
     */
    protected function roleLevel($role)
    {
        $levels = [
            'admin' => 3,
            'editor' => 2,
            'author' => 1
        ];

        return $levels[$role] ?? 0;
    }
}