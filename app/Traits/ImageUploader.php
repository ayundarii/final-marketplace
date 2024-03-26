<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait ImageUploader {

    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function uploadImage(Request $request, string $fieldName, string $destinationPath): string
    {
        // Get the uploaded image file
        $image = $request->file($fieldName);

        // Generate a unique filename
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();

        // Store the image in the specified destination path
        $image->storeAs($destinationPath, $filename);

        // Return the stored filename
        return $filename;
    }
}