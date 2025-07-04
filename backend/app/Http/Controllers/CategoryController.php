<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ResponseTrait;


class CategoryController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $categories = Category::all();
            return $this->successResponse($categories, 'Categories fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch categories', 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return $this->successResponse($category, 'Category fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Category not found', 404);
        }
    }
}
