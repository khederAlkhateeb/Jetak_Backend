<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\AdminUserController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\TransactionController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
    Route::put('/update-housing-info', [ProfileController::class, 'updateHousingInfo'])->middleware('auth:sanctum');
});


Route::middleware(['auth:sanctum', 'role:Super Admin'])
    ->prefix('v1/SuperAdmin')
    ->group(function () {
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::put('/users/{user}/promote-to-delivery', [AdminUserController::class, 'promoteToDelivery']);
        Route::patch('/users/{user}/status', [AdminUserController::class, 'updateStatus']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
});


Route::middleware(['auth:sanctum'])
    ->prefix('v1/wallet')
    ->group(function () {
        Route::get('/balance', [WalletController::class, 'balance']);
        Route::get('/transactions', [WalletController::class, 'transactions']);
    });

Route::middleware(['auth:sanctum', 'role:Super Admin'])
    ->prefix('v1/wallet')
    ->group(function () {
        Route::post('/deposit', [WalletController::class, 'deposit']);
        Route::post('/withdraw', [WalletController::class, 'withdraw']);
    });


Route::middleware(['auth:sanctum', 'role:Super Admin'])
    ->prefix('v1/transactions')
    ->group(function () {
        Route::get('/', [TransactionController::class, 'transactions']);
        Route::get('/{transaction}', [TransactionController::class, 'showTransaction']);
    });