<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function uploadTempImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            ]);

            $image = $request->file('image');
            $imagePath = $image->store('temp', 'public');
            $imageUrl = Storage::url($imagePath);

            Log::info('Temporary image uploaded:', ['path' => $imagePath, 'url' => $imageUrl]);

            return response()->json(['imageUrl' => $imageUrl]);
        } catch (\Exception $e) {
            Log::error('Image upload failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to upload image: ' . $e->getMessage()], 500);
        }
    }
}