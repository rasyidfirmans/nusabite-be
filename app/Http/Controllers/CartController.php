<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteItemRequest;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = Cart::with(['products', 'products.category'])->first();
        if (!$cart) {
            return response()->json([
                'code' => 404,
                'message' => 'Cart not found',
            ], 404);
        }

        $cart->products->each(function ($product) {
            $product->quantity = $product->pivot->quantity;
            $category_name = $product->category->name;
            unset($product->category);
            $product->category = $category_name;

            unset($product->pivot);
            unset($product->category_id);
        });

        return response()->json([
            'code' => 200,
            'message' => 'Cart retrieved successfully',
            'data' => $cart,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartRequest $request)
    {
        $cart = Cart::find($request->cart_id);
        if (!$cart) {
            $cart = Cart::create();
        }
        if ($cart->products()->where('product_id', $request->product_id)->exists()) {
            $quantity = $cart->products()->where('product_id', $request->product_id)->first()->pivot->quantity;
            $cart->products()->updateExistingPivot($request->product_id, [
                'quantity' => $quantity + $request->quantity,
            ]);
        } else {
            $cart->products()->attach($request->product_id, [
                'quantity' => $request->quantity,
            ]);
        }

        $cart->load(['products', 'products.category']);
        $cart->products->each(function ($product) {
            $product->quantity = $product->pivot->quantity;
            $category_name = $product->category->name;
            unset($product->category);
            $product->category = $category_name;

            unset($product->pivot);
            unset($product->category_id);
        });

        return response()->json([
            'code' => 201,
            'message' => 'Cart created successfully',
            'data' => $cart,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        if (!$cart) {
            return response()->json([
                'code' => 404,
                'message' => 'Cart not found',
            ], 404);
        }

        if ($cart->products()->where('product_id', $request->product_id)->exists()) {
            $cart->products()->updateExistingPivot($request->product_id, [
                'quantity' => $request->quantity,
            ]);
        }

        $cart->load(['products', 'products.category']);
        $cart->products->each(function ($product) {
            $product->quantity = $product->pivot->quantity;
            $category_name = $product->category->name;
            unset($product->category);
            $product->category = $category_name;

            unset($product->pivot);
            unset($product->category_id);
        });

        return response()->json([
            'code' => 200,
            'message' => 'Cart updated successfully',
            'data' => $cart,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        if (!$cart) {
            return response()->json([
                'code' => 404,
                'message' => 'Cart not found',
            ], 404);
        }

        $cart->products()->detach();

        return response()->json([
            'code' => 200,
            'message' => 'Cart was cleared successfully',
        ]);
    }

    public function deleteItem(DeleteItemRequest $request)
    {
        $product_item = Cart::with('products')
            ->whereHas('products', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })->first();

        if (!$product_item) {
            return response()->json([
                'code' => 404,
                'message' => 'Product not found in cart',
            ], 404);
        } else {
            $product_item->products()->detach($request->product_id);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Item removed from cart successfully',
        ]);
    }
}
