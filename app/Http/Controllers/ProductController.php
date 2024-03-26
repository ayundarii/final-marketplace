<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Image;
use App\Traits\ImageUploader;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ImageUploader;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve products with eager loading of image
        $products = Product::with('category', 'brand', 'image', 'user')->get();

        return response()->json([
            'message' => 'Products successfully retrieved',
            'products' => $products, 
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' =>'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id', 
            'brand_id' => 'required|exists:brands,id',
            'user_id' => 'required|exists:users,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        Log::info('Product store request:', $request->all());

        // Fill product attributes 
        $product = new Product;
        $product->fill($request->except('image')); 
        
        // Save product
        $product->save();

        // Upload the image and store the filename
        $filename = $this->uploadImage($request, 'image', 'public/images/products');
        $image = new Image;
        $image->url = $filename;
        $image->imageable_id = $product->id;
        $image->imageable_type = Product::class;

        // Associate the image with the saved product
        $product->image()->save($image);

        return response()->json([
            'message' => 'Product succesfully made.',
            'product' => $product->load('category', 'brand', 'user', 'image'), 
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category', 'brand', 'image')->find($id);

        if ($product) {
            return response()->json([
                'message' => 'Product successfully retrieved',
                'product' => $product, 
            ], 200);
        } else {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
