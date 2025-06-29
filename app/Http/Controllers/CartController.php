<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Resources\CartResource;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\DeleteItemRequest;
use App\Http\Requests\UpdateCartRequest;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart_id = request()->query('id', null);

        $cart = Cart::with(['products', 'products.category'])
            ->where('id', $cart_id)
            ->first();

        if (!$cart) {
            return response()->json([
                'code' => 404,
                'message' => 'Cart not found',
            ], 404);
        }

        $cartResource = new CartResource($cart);

        return response()->json([
            'code' => 200,
            'message' => 'Cart retrieved successfully',
            'data' => $cartResource,
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
        $cart = new CartResource($cart);

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

        $cartResource = new CartResource($cart);

        return response()->json([
            'code' => 200,
            'message' => 'Cart updated successfully',
            'data' => $cartResource,
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Cart $cart)
    {
        $product_id = $request->query('id', null);
        if (!$cart) {
            return response()->json([
                'code' => 404,
                'message' => 'Cart not found',
            ], 404);
        }

        if ($product_id) {
            if (!$cart->products()->where('product_id', $product_id)->exists()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Product not found in cart',
                ], 404);
            }

            $cart->products()->detach($product_id);

            return response()->json([
                'code' => 200,
                'message' => 'Item removed from cart successfully',
            ]);
        } else {
            $cart->products()->detach();
        }

        return response()->json([
            'code' => 200,
            'message' => 'Cart was cleared successfully',
        ]);
    }
}
