<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\V1\Authentication\LoginUserAction;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use  App\Actions\V1\Authentication\RegisterUserAction;
use App\Actions\V1\Authentication\LogoutUserAction;
use App\Actions\V1\Authentication\MeUserAction;
use App\Actions\V1\Authentication\ChangePasswordAction;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;


/**
 * Handles authentication-related HTTP requests, including user
 * registration, login, and logout.
 *
 * Delegates all business logic to dedicated Action classes
 * (RegisterUser, LoginUser, LogoutUser) and is only responsible
 * for request/response orchestration.
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param LoginUserAction $loginUserAction
     */
    public function __construct(
        private readonly LoginUserAction $loginUserAction,
        private readonly RegisterUserAction $registerUserAction,
        private readonly LogoutUserAction $logoutUserAction,
        private readonly MeUserAction $meUserAction,
        private readonly ChangePasswordAction $changePasswordAction
    ) {}

    /**
     * Handle user login requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->loginUserAction->execute($validatedData);

        return $this->successResponse(
            data: [
                'user' => $result['user'],
                'token' => $result['token'],
            ],
            message: 'Logged in successfully',
            code: 200
        );
    }

    /**
     * Handle user registration requests.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $result = $this->registerUserAction->execute($validatedData);

        return $this->successResponse(
            data: [
                'user' => $result['user'],
                'token' => $result['token'],
            ],
            message: 'Registered successfully',
            code: 201
        );
    }

    /**
     * Handle user logout requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->logoutUserAction->execute($request->user());

        return $this->successResponse(
            message: 'Logged out successfully',
            code: 200
        );
    }

    /**
     * Retrieve the authenticated user with their related data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $this->meUserAction->execute($request->user());

        return $this->successResponse(
            data: [
                'user' => $user,
            ],
            message: 'User retrieved successfully',
            code: 200
        );
    }


    /**
     * Handle user password change requests.
     *
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $validatedData = $request->validated();

        $this->changePasswordAction->execute($request->user(), $validatedData);

        return $this->successResponse(
            message: 'Password changed successfully',
            code: 200
        );
    }
}
