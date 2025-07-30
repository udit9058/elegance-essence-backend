<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SellerUser;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class DeleteExpiredProducts extends Command
{
    protected $signature = 'products:delete-expired';
    protected $description = 'Delete all products of sellers whose plan duration has expired';

    public function handle()
    {
        $sellers = SellerUser::whereNotNull('plan_duration')
            ->whereNotNull('created_at')
            ->get();

        foreach ($sellers as $seller) {
            $expirationTime = $seller->created_at->addMinutes($seller->plan_duration);
            if (now()->greaterThanOrEqualTo($expirationTime)) {
                $products = Product::where('seller_id', $seller->id)->get();
                foreach ($products as $product) {
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $product->delete();
                }
            }
        }
    }
}