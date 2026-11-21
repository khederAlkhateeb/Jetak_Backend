<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\V1\Wallet\GetWalletBalanceAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Wallet\WalletBalanceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Wallet\ListWalletTransactionsRequest;
use App\Http\Resources\V1\Wallet\WalletTransactionResource;
use App\Actions\V1\Wallet\GetWalletTransactionsAction;
use App\Actions\V1\SuperAdmin\DepositWalletAction;
use App\Http\Requests\Api\SuperAdmin\DepositWalletRequest;
use App\Actions\V1\SuperAdmin\WithdrawWalletAction;
use App\Http\Requests\Api\SuperAdmin\WithdrawWalletRequest;


class WalletController extends Controller
{
    public function __construct(
        private readonly GetWalletBalanceAction $getWalletBalanceAction,
        private readonly GetWalletTransactionsAction $getWalletTransactionsAction,
        private readonly DepositWalletAction $depositWalletAction,
        private readonly WithdrawWalletAction $withdrawWalletAction,
    ) {}

    /**
     * Get the authenticated user balance
     * @param Request $request
     * @return JsonResponse
     */
    public function balance(Request $request): JsonResponse
    {
        $wallet = $this->getWalletBalanceAction->execute($request->user());

        return $this->successResponse(
            data: [
                'wallet' => new WalletBalanceResource($wallet),
            ],
            message: 'Wallet balance retrieved successfully',
            code: 200
        );
    }



    /**
     * Get the authenticated user transactions
     * @param ListWalletTransactionsRequest $request
     * @return JsonResponse
     */
    public function transactions(ListWalletTransactionsRequest $request)
    {
        $transactions = $this->getWalletTransactionsAction->execute(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            data: [
                'transactions' => WalletTransactionResource::collection($transactions),
            ],
            message: 'Wallet transactions retrieved successfully',
            code: 200
        );
    }


    /**
     * Deposit an amount in to an account
     * @param DepositWalletRequest $request
     * @return JsonResponse
     */
    public function deposit(DepositWalletRequest $request): JsonResponse
    {
        $wallet = $this->depositWalletAction->execute($request->validated());

        return $this->successResponse(
            data: [
                'wallet' => new WalletBalanceResource($wallet),
            ],
            message: 'Wallet deposited successfully',
            code: 201
        );
    }


    /**
     * Withdraw an amount from an account
     * @param WithdrawWalletRequest $request
     * @return JsonResponse
     */
    public function withdraw(WithdrawWalletRequest $request): JsonResponse
    {
        $wallet = $this->withdrawWalletAction->execute($request->validated());

        return $this->successResponse(
            data: [
                'wallet' => new WalletBalanceResource($wallet),
            ],
            message: 'Wallet withdrawn successfully',
            code: 200
        );
    }
}
