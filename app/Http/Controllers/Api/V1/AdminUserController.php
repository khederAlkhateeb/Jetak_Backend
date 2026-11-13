<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SuperAdmin\ListUsersRequest;
use App\Actions\V1\SuperAdmin\GetAdminUsersAction;
use App\Http\Resources\V1\AdminUserResource;
use App\Actions\V1\SuperAdmin\CreateAdminUserAction;
use App\Http\Requests\Api\SuperAdmin\CreateAdminUserRequest;
use App\Actions\V1\SuperAdmin\GetUserDetailsAction;
use App\Http\Resources\V1\UserDetailsResource;
use App\Models\User;
use App\Actions\V1\SuperAdmin\PromoteToDeliveryAction;
use App\Actions\V1\SuperAdmin\UpdateUserStatusAction;
use App\Http\Requests\Api\SuperAdmin\UpdateUserStatusRequest;
use App\Actions\V1\SuperAdmin\DeleteUserAction;

class AdminUserController extends Controller
{
    /**
     * @param GetAdminUsersAction $getAdminUsersAction
     * @param CreateAdminUserAction $createAdminUserAction
     * @param GetUserDetailsAction $getUserDetailsAction
     */
    public function __construct(
        private readonly GetAdminUsersAction $getAdminUsersAction,
        private readonly CreateAdminUserAction $createAdminUserAction,
        private readonly GetUserDetailsAction $getUserDetailsAction,
        private readonly PromoteToDeliveryAction $promoteToDeliveryAction,
        private readonly UpdateUserStatusAction $updateUserStatusAction,
        private readonly DeleteUserAction $deleteUserAction,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ListUsersRequest $request)
    {
        $users = $this->getAdminUsersAction->execute($request->validated());

        return $this->successResponse(
            data: AdminUserResource::collection($users),
            message: 'Users retrieved successfully.',
            code: 200
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAdminUserRequest $request)
    {
        $user = $this->createAdminUserAction->execute($request->validated());

        return $this->successResponse(
            data: new AdminUserResource($user),
            message: 'User created successfully.',
            code: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $userDetails = $this->getUserDetailsAction->execute($user);

        return $this->successResponse(
            data: new UserDetailsResource($userDetails),
            message: 'User details retrieved successfully.',
            code: 200
        );
    }

    /**
     * Promote a user to Delivery role.
     */
    public function promoteToDelivery(User $user)
    {
        $user = $this->promoteToDeliveryAction->execute($user);

        return $this->successResponse(
            data: new AdminUserResource($user),
            message: 'User promoted to Delivery role successfully.',
            code: 200
        );
    }

    /**
     * Update the status of the specified user.
     */
    public function updateStatus(UpdateUserStatusRequest $request, User $user)
    {
        $user = $this->updateUserStatusAction->execute($user, $request->validated()['status']);

        return $this->successResponse(
            data: new AdminUserResource($user),
            message: 'User status updated successfully.',
            code: 200
        );
    }

    /**
     * Remove user from the system.
     */
    public function destroy(User $user)
    {
        $this->deleteUserAction->execute($user);

        return $this->successResponse(
            message: 'User deleted successfully.',
            code: 200
        );
    }
}
