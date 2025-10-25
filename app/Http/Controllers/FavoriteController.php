<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Products $product)
    {
        Auth::user()->favorites()->attach($product->product_id);

        return back()->with('success', 'Product added to favorites!');
    }

    public function destroy(Products $product)
    {
        Auth::user()->favorites()->detach($product->product_id);

        return back()->with('success', 'Product removed from favorites!');
    }

    // Toggle favorite status
    public function toggle(Products $product)
    {
        $user = Auth::user();

        if ($user->favorites()->where('product_id', $product->product_id)->exists()) {
            $user->favorites()->detach($product->product_id);
            $message = 'Product removed from favorites!';
        } else {
            $user->favorites()->attach($product->product_id);
            $message = 'Product added to favorites!';
        }

        return back()->with('success', $message);
    }
}
