<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetProductsByCategoryRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        $products = Product::with('category')->get();

        if ($category) {
            $products = Product::with('category')
                ->whereHas('category', function ($query) use ($category) {
                    $query->where('name', $category);
                })
                ->get();
        } else {
            $products = Product::with('category')->get();
        }

        $products = ProductResource::collection($products);

        return response()->json([
            'code' => 200,
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category');
        if (!$product) {
            return response()->json([
                'code' => 404,
                'message' => 'Product not found',
            ], 404);
        }

        $productData = new ProductResource($product);

        return response()->json([
            'code' => 200,
            'message' => 'Product retrieved successfully',
            'data' => $productData,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
