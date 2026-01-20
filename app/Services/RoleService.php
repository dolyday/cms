<?php

namespace App\Services;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

class RoleService
{
    /**
     * Retrieves a paginated list of roles, optionally filtered by a search term.
     *
     * @param Request $request
     * @return array
     */
    public function allRoles(Request $request): array
    {
        $perPage = $request->input('perPage', 10);
        $roles = Role::latest('id');

        if ($request->has('search')) {
            $search = $request->get('search');
            $roles = $roles->where('name', 'LIKE', "%{$search}%");
        }

        $roles = $roles->paginate($perPage);

        return $data;
    }


    /**
     * Create a new role.
     *
     * @param Request $request
     * @return array
     */
    public function createRole(Request $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        $role->givePermissionTo($request->permissions);

        return [
            'message' => 'Added successfully.'
        ];
    }


    /**
     * Retrieve a role by ID.
     *
     * @param string $id
     * @return array
     */
    public function getRole(string $id): array
    {
        $role = Role::find($id);

        if (!$role) {
            return [
                'error' => 'Record not found.'
            ];
        }

        return [
            'role' => $role,
            'role_permissions' => $role->permissions->pluck('name')
        ];
    }


    /**
     * Update an existing role.
     *
     * @param Request $request
     * @param string $id
     * @return array
     */
    public function updateRole(Request $request, string $id): array
    {
        $role = Role::find($id);

        if (!$role) {
            return [
                'error' => 'Record not found.'
            ];
        }

        Gate::authorize('update', $role);

        $role->update([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        $role->syncPermissions($request->permissions);

        return [
            'message' => 'Updated successfully.'
        ];
    }


    /**
     * Delete an existing role.
     *
     * @param string $id
     * @return array
     */
    public function deleteRole(string $id): array
    {
        $role = Role::find($id);

        if (!$role) {
            return [
                'error' => 'Record not found.'
            ];
        }

        Gate::authorize('delete', $role);

        if ($role->users->count()) {
            return [
                'error' => 'Role already used.'
            ];
        }

        $role->revokePermissionTo($role->permissions->pluck('name'));
        $role->delete();

        return [
            'message' => 'Deleted successfully.'
        ];
    }
}