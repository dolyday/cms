<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
        $userRole = $user->getRoleNames()->first();
        $categoryUserRole = $category->user->getRoleNames()->first();

        return $user->id === $category->user_id ||
            $this->roleLevel($userRole) > $this->roleLevel($categoryUserRole);
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