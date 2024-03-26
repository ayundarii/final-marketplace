<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Image;
use App\Traits\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    use ImageUploader;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::with('image')->get();
        return response()->json([
            'message' => 'Brands succesfully retrieved',
            'brands' => $brands
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
    
        $brand = new Brand;
        $brand->name = $request->name;
        $brand->description = $request->description;

        // Handle image upload and create Image model
        $filename = $this->uploadImage($request, 'image', 'public/images/brands');
        $image = new Image;
        $image->url = $filename;
        $image->imageable_id = $brand->id;
        $image->imageable_type = Brand::class;

        $brand->save();
        $brand->image()->save($image); 
    
        return response()->json([
            'message' => 'Brand created successfully',
            'brand' => $brand->load('image'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    
        return response()->json([
            'message' => 'Brand succesfully retrieved',
            'brand' => $brand->load('image')
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

        Log::info('Brand update request:', $request->all());
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $brand = Brand::find($id);
    
        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    
        $brand->name = $request->name;
        $brand->description = $request->description;
        $brand->save();

        //IMAGE UPDATED NOT YET
    
        return response()->json([
            'message' => 'Brand updated successfully',
            'brand' => $brand,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        if ($brand->image) {
            Storage::delete($brand->image->url);  
            $brand->image()->delete(); 
        }

        $brand->delete();

        return response()->json([
            'message' => 'Brand deleted successfully',
            'brand' => $brand,
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $brand = Brand::withTrashed()->where('id', $id)->firstOrFail();

        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        // Restore the associated image (if any)
        if ($brand->image && $brand->image->trashed()) {
            $brand->image->restore();
        }

        $brand->restore();

        return response()->json([
            'message' => 'Brand restored successfully',
            'brand' => $brand,
        ], 200);
    }
}
