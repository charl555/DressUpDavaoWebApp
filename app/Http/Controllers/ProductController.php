<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Shops;

class ProductController extends Controller
{
    public function show($product_id)
    {
        $product = Products::with('product_images', 'user.shop')
            ->where('product_id', $product_id)
            ->firstOrFail();

        return view('ProductOverview', compact('product'));
    }

    public function shopOverview(Shops $shop)
    {
        $products = Products::where('visibility', 'Yes')
            ->where('user_id', $shop->user_id)
            ->with('product_images')
            ->paginate(12);

        return view('ShopOverview', compact('shop', 'products'));
    }
}
