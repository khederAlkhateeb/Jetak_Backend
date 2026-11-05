<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\V1\Profile\UpdateHousingInfoAction;
use App\Http\Requests\Api\Profile\UpdateHousingRequest;

/**
 * Handles Profile-related HTTP requests, including updating user housing information.
 *
 * Delegates all business logic to dedicated Action classes
 *(UpdateHousingInfoAction) and is only responsible
 * for request/response orchestration.
 *
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * Create a new instance of the controller.
     * @param UpdateHousingInfoAction $updateHousingInfoAction
     */
    public function __construct(
        private readonly UpdateHousingInfoAction $updateHousingInfoAction
    ) {}
    

    /**
     * Update the user's housing information.
     *
     * @param UpdateHousingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHousingInfo(UpdateHousingRequest $request){
        
        $validatedData = $request->validated();
        $user = $request->user();

        $this->updateHousingInfoAction->execute($user, $validatedData);

        return $this->successResponse(
            data: $user->fresh(),
            message: 'Housing information updated successfully',
            code: 200
        );
    }
}
