<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with('products.category')->get();

        $transactions->each(function ($transaction) {
            $transaction->products->each(function ($product) {
                $product->quantity = $product->pivot->quantity;
                $category_name = $product->category->name;
                unset($product->category);
                $product->category = $category_name;
                unset($product->category_id);
                unset($product->pivot);
            });
        });

        return response()->json([
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

        $transaction->products->each(function ($product) {
            $product->quantity = $product->pivot->quantity;
            $category_name = $product->category->name;
            unset($product->category);
            $product->category = $category_name;
            unset($product->category_id);
            unset($product->pivot);
        });

        return response()->json([
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
