<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetProductsByCategoryRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();

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

        $productData = $product->toArray();
        if ($product->category) {
            $productData['category_name'] = $product->category->name;
        }
        unset($productData['category']);
        unset($productData['category_id']);

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

    public function getProductsByCategory(GetProductsByCategoryRequest $request)
    {
        $products = Product::where('category_id', $request->category_id)->with('category')->get();

        $products->each(function ($product) {
            $category_name = $product->category->name;
            unset($product->category);
            $product->category = $category_name;
            unset($product->category_id);
        });

        return response()->json([
            'code' => 200,
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ]);
    }
}
