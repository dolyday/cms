<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Retrieves a paginated list of users, optionally filtered by a search term.
     *
     * @param Request $request
     * @return array
     */
    public function allUsers(Request $request): array
    {
        $perPage = $request->input('perPage', 10);
        $users = User::latest('id');

        if ($request->has('search')) {
            $search = $request->get('search');
            $users = $users->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        }

        $data = $users->paginate($perPage);

        return [
            'data' => $data
        ];
    }


    /**
     * Create a new user and assign selected permissions to it.
     *
     * @param Request $request
     * @return array
     */
    public function createUser(Request $request): array
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($request->status) {
            $user->status = $request->status;
        }

        $user->save();

        $user->assignRole($request->role);

        return [
            'message' => 'Added successfully.'
        ];
    }


    /**
     * Retrieve a user by ID.
     *
     * @param string $id
     * @return array
     */
    public function getUser(string $id): array
    {

        $user = User::find($id);

        if (!$user) {
            return sendError([
                'error' => 'Record not found.'
            ], 404);
        }

        return [
            'user' => $user
        ];
    }


    /**
     * Update an existing user name.
     *
     * @param Request $request
     * @param string $id
     * @return array
     */
    public function updateUser(Request $request, string $id): array
    {
        $user = User::find($id);

        if (!$user) {
            return sendError([
                'error' => 'Record not found.'
            ], 404);
        }

        Gate::authorize('update', $user);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->status) {
            $user->status = $request->status;
        }

        $user->save();

        $user->syncRoles($request->role);

        return [
            'message' => 'Updated successfully.'
        ];
    }


    /**
     * Delete a user if it's not currently assigned to any users.
     *
     * @param string $id
     * @return array
     */
    public function deleteUser(string $id): array
    {
        $user = User::find($id);

        if (!$user) {
            return [
                'error' => 'Record not found.'
            ];
        }

        Gate::authorize('delete', $user);

        $user->removeRole($user->getRoleNames()->first());
        $user->delete();

        return [
            'message' => 'Deleted successfully.'
        ];
    }
}