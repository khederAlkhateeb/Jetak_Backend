<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\V1\SuperAdmin\GetAdminTransactionsAction;
use App\Http\Requests\Api\SuperAdmin\ListAdminTransactionsRequest;
use App\Http\Resources\SuperAdmin\AdminTransactionResource;
use App\Actions\V1\SuperAdmin\GetAdminTransactionDetailsAction;
use App\Http\Resources\SuperAdmin\AdminTransactionDetailsResource;


class TransactionController extends Controller
{
    public function __construct(
        private readonly GetAdminTransactionsAction $getAdminTransactionsAction,
        private readonly GetAdminTransactionDetailsAction $getAdminTransactionDetailsAction,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function transactions(ListAdminTransactionsRequest $request)
    {
        $transactions = $this->getAdminTransactionsAction->execute($request->validated());

        return $this->successResponse(
            data: AdminTransactionResource::collection($transactions),
            message: 'Transactions retrieved successfully.',
            meta: [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
            ],
            code: 200
        );
    }


    public function showTransaction(Transaction $transaction)
{
    $transactionDetails = $this->getAdminTransactionDetailsAction->execute($transaction);

    return $this->successResponse(
        data: [
            'transaction' => new AdminTransactionDetailsResource($transactionDetails),
        ],
        message: 'Transaction details retrieved successfully',
        code: 200
    );
}

}
