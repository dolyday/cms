<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * Class constructor.
     *
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->middleware('permission:show_roles', ['only' => ['index', 'show']]);
        $this->middleware('permission:add_role', ['only' => 'store']);
        $this->middleware('permission:update_role', ['only' => 'update']);
        $this->middleware('permission:delete_role', ['only' => 'destroy']);

        $this->roleService = $roleService;
    }


    /**
     * Display a listing of roles with permissions.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->roleService->allRoles($request);

        return sendResponse($data);
    }


    /**
     * Store a newly created role.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleRequest $request)
    {
        $result = $this->roleService->createRole($request);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result, 422);
    }


    /**
     * Display the specified role.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $result = $this->roleService->getRole($id);

        return isset($result['error'])
            ? sendError($result, 404)
            : sendResponse($result);
    }


    /**
     * Update the specified role.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleRequest $request, string $id)
    {
        $result = $this->roleService->updateRole($request, $id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result);
    }


    /**
     * Remove the specified role.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $result = $this->roleService->deleteRole($id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result);
    }
}