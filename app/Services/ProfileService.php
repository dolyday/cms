<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileService
{
    /**
     * Get the authenticated user profile details.
     *
     * @return array
     */
    public function getDetails(): array
    {
        return [
            'profile' => auth()->user()
        ];
    }


    /**
     * Update profile information.
     *
     * @param Request $request
     * @return array
     */
    public function updateProfile(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:25',
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            return [
                'errors' => $validator->errors()
            ];
        }

        $profile = auth()->user();

        $profile->update([
            'name'  => $request->name,
            'email' => $request->email
        ]);

        return [
            'message' => 'Updated successfully.'
        ];
    }


    /**
     * Change password of the authenticated user.
     *
     * @return array
     */
    public function changePassword(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return [
                'errors' => $validator->errors()
            ];
        }

        $user = User::find(auth()->id());

        if (!Hash::check($request->old_password, $user->password)) {
            return [
                'errors' => ['old_password' => ['The old password is incorrect.']]
            ];
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return [
            'message' => 'Password changed successfully.'
        ];
    }
}