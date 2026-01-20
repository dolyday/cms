<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    /**
     * Register a new user and assign default role.
     *
     * @param Request $request
     * @return array
     */
    public function register(Request $request): array
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('author');

        return [
            'user' => $user
        ];
    }
}