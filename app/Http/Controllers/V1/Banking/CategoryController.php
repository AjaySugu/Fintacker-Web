<?php

namespace App\Http\Controllers\V1\Banking;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function storeIcon(Request $request) {
        try {
        // UPDATE case
        if ($request->id !== null) {
            $category = Category::where('id', $request->id)
                                ->where('type', $request->type)
                                ->first();

            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found'
                ], 404);
            }
        } 
        
        // CREATE case
        else {
            $category = new Category();
            $category->type = $request->type; 
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');
            $category->icon = $path;
        }

        $category->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Category saved successfully',
            'data'    => $category
        ]);

    } catch (\Exception $e) {
        \Log::error('BudgetController->store ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'Failed to create budget'
        ]);
    }
}
}
