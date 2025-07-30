<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use GrokPHP\Laravel\Facades\GrokAI;
use GrokPHP\Client\Config\ChatOptions;
use GrokPHP\Client\Enums\Model;
use Illuminate\Support\Facades\Log;
use GrokPHP\Client\Exceptions\GrokException;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('stock', '>', 0)
            ->select('id', 'name', 'description', 'price', 'stock', 'image')
            ->get();
        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product = Product::create($validated);
        return response()->json($product, 201);
    }
}
