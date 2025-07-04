<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ResponseTrait;


class ProductController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $products = Product::with('category')->get();

            $transformedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->unit_price,
                    'categoryId' => $product->category_id,
                    'category' => $product->category ? $product->category->name : 'Unknown',
                ];
            });

            return $this->successResponse($transformedProducts, 'Products fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch products', 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);
            return $this->successResponse($product, 'Product fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Product not found', 404);
        }
    }
}
