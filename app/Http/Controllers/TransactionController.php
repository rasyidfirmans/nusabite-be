<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with('products.category')->get();

        $transactions = TransactionResource::collection($transactions);

        return response()->json([
            'code' => 200,
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $transaction = Transaction::create([
            'status' => 'Pending',
        ]);

        foreach ($request->products as $product) {
            $transaction->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
            ]);
        }

        $transaction->load('products.category');

        $transaction = new TransactionResource($transaction);

        return response()->json([
            'code' => 201,
            'message' => 'Transaction created successfully',
            'data' => $transaction,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
