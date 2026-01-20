<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\ProfileService;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * @var ProfileService
     */
    protected $profileService;


    /**
     * Class constructor.
     *
     * @param ProfileService $profileService
     */
    public function __construct(ProfileService $profileService)
    {
        $this->middleware('role:admin', ['only' => 'update']);
        $this->profileService = $profileService;
    }


    /**
     * Get the details of the authenticated user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function details(Request $request)
    {
        $data = $this->profileService->getDetails();

        return sendResponse($data);
    }


    /**
     * Update the authenticated user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $result = $this->profileService->updateProfile($request);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result, 422);
    }


    /**
     * Change password of the authenticated user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $result = $this->profileService->changePassword($request);

        return isset($result['errors'])
            ? sendError($result, 422)
            : sendResponse($result);
    }
}