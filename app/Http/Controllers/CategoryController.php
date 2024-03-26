<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use App\Traits\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use ImageUploader;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('image')->get();
        return response()->json([
            'message' => 'Categories succesfully retrieved',
            'categories' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string', 
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;

        // Handle image upload and create Image model
        $filename = $this->uploadImage($request, 'image', 'public/images/categories');
        $image = new Image;
        $image->url = $filename;
        $image->imageable_id = $category->id;
        $image->imageable_type = Category::class;

        $category->save();
        $category->image()->save($image); 
    
        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category->load('image'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
    
        return response()->json([
            'message' => 'Category succesfully retrieved',
            'category' => $category->load('image')
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        Log::info('Category update request:', $request->all());
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $category = Category::find($id);
    
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
    
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        //IMAGE UPDATED NOT YET
    
        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
