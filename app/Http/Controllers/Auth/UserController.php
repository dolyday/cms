<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Class constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('permission:show_users', ['only' => ['index', 'show']]);
        $this->middleware('permission:add_user', ['only' => 'store']);
        $this->middleware('permission:update_user', ['only' => 'update']);
        $this->middleware('permission:delete_user', ['only' => 'destroy']);

        $this->userService = $userService;
    }


    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->userService->allUsers($request);

        return sendResponse($data);
    }


    /**
     * Store a newly created user.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        $result = $this->userService->createUser($request);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result, 422);
    }


    /**
     * Display the specified user.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $result = $this->userService->getUser($id);

        return isset($result['error'])
            ? sendError($result, 404)
            : sendResponse($result);
    }


    /**
     * Update the specified user.
     *
     * @param UserRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, string $id)
    {
        $result = $this->userService->updateUser($request, $id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result);
    }


    /**
     * Remove the specified user.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $result = $this->userService->deleteUser($id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result);
    }
}