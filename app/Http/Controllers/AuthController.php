<?php

namespace App\Http\Controllers;

use App\Services\LoginService;
use App\Services\RegisterService;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    /**
     * @var LoginService
     */
    protected $loginService;

    /**
     * @var registerService
     */
    protected $registerService;

    /**
     * Class constructor.
     *
     * @param LoginService $loginService
     * @param RegisterService $registerService
     */
    public function __construct(LoginService $loginService, RegisterService $registerService)
    {
        $this->loginService = $loginService;
        $this->registerService = $registerService;
    }


    /**
     * Handle a register request for a user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {

        $data = $this->registerService->register($request);

        return sendResponse($data);
    }


    /**
     * Handle a login request for a user.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {

        $data = $this->loginService->login($request);

        return sendResponse($data);
    }
}