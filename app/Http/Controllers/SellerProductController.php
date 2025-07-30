<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SellerProductController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::guard('seller')->user();
        if (!$seller) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $products = Product::where('seller_id', $seller->id)->get();
        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $seller = Auth::guard('seller')->user();
        if (!$seller) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $productCount = Product::where('seller_id', $seller->id)->count();
        if ($productCount >= 5) {
            return response()->json(['message' => 'Maximum limit of 5 products reached.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $seller = Auth::guard('seller')->user();
        if (!$seller) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = Product::where('id', $id)->where('seller_id', $seller->id)->first();
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ], 200);
    }

    public function delete($id)
    {
        $seller = Auth::guard('seller')->user();
        if (!$seller) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = Product::where('id', $id)->where('seller_id', $seller->id)->first();
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}