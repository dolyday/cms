<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    /**
     * Attempt to log the user in.
     *
     * @param Request $request
     * @return array
     */
    public function login(Request $request): array
    {
        $status = $this->getStatus($request);

        if ($status === 'off') {
            return [
                'message' => 'Your account is not verified yet.'
            ];
        }

        return $this->validateInfo($request);
    }


    /**
     * Validate email and password.
     *
     * @param Request $request
     * @return array
     */
    protected function validateInfo(Request $request): array
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'error' => 'Email / Password is invalid.'
            ];
        }

        return [
            'user' => $user,
            'token' => $user->createToken('token')->plainTextToken
        ];
    }


    /**
     * Get account status for the given email.
     *
     * @param Request $request
     * @return string|null
     */
    protected function getStatus(Request $request): string|null
    {
        $user = User::where('email', $request->email)->first();

        return $user?->status;
    }
}