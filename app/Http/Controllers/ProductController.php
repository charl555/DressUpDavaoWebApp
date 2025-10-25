<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Shops;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($product_id)
    {
        $product = Products::with([
            'product_images',
            'user.shop',  // This loads user and their shop
            'product_measurements',
            'occasions',
            'product_3d_models'
        ])->where('product_id', $product_id)->firstOrFail();

        echo '<script>window.productData = ' . json_encode([
            'id' => $product->product_id,
            'name' => $product->name,
            'shopId' => $product->user->shop->shop_id ?? null,
            'shop' => $product->user->shop->shop_name ?? 'Unknown Shop',
            'owner' => $product->user->name,
            'thumbnail' => $product->product_images->first()->thumbnail_image ?? null
        ]) . ';</script>';

        return view('ProductOverview', compact('product'));
    }

    // public function shopOverview(Shops $shop)
    // {
    //     $products = Products::where('visibility', 'Yes')
    //         ->where('user_id', $shop->user_id)
    //         ->with('product_images')
    //         ->paginate(12);

    //     return view('ShopOverview', compact('shop', 'products'));
    // }

    public function productList(Request $request)
    {
        $query = Products::where('visibility', 'Yes')
            ->with(['product_images', 'occasions', 'user.shop', 'product_3d_models']);

        // Gender-based category filtering
        $userGender = auth()->check() ? auth()->user()->gender : null;

        if ($userGender === 'Female') {
            // Show only Gowns for female users
            $query->where('type', 'Gown');
        } elseif ($userGender === 'Male') {
            // Show only Suits for male users
            $query->where('type', 'Suit');
        }
        // For guests or users with other/prefer not to say gender, show all products

        // Subtype filter
        if ($request->filled('subtype')) {
            $query->whereIn('subtype', (array) $request->subtype);
        }

        // Size filter
        if ($request->filled('size')) {
            $sizeMapping = [
                'XS' => 'Extra Small',
                'S' => 'Small',
                'M' => 'Medium',
                'L' => 'Large',
                'XL' => 'Extra Large',
                'XXL' => 'Extra Extra Large'
            ];
            $dbSizes = array_map(fn($s) => $sizeMapping[$s] ?? $s, (array) $request->size);
            $query->whereIn('size', $dbSizes);
        }

        // Color filter
        if ($request->filled('color')) {
            $colors = (array) $request->color;
            $query->where(function ($q) use ($colors) {
                foreach ($colors as $color) {
                    $q->orWhere('colors', 'LIKE', "%$color%");
                }
            });
        }

        // Occasion filter
        if ($request->filled('occasion')) {
            $occasions = (array) $request->occasion;
            $query->whereHas('occasions', function ($q) use ($occasions) {
                $q->whereIn('occasion_name', $occasions);
            });
        }

        // 🧍‍♀️ Body Measurement Filter
        if ($request->has('measurements_filter') && auth()->check()) {
            $userMeasurements = \App\Models\UserMeasurements::where('user_id', auth()->id())->first();

            if ($userMeasurements) {
                $tolerance = 2;  // inches difference allowed

                $query->whereHas('product_measurements', function ($q) use ($userMeasurements, $tolerance) {
                    // ✅ Check for gown fits
                    $q
                        ->where(function ($qq) use ($userMeasurements, $tolerance) {
                            $qq
                                ->whereBetween('gown_chest', [
                                    $userMeasurements->chest - $tolerance,
                                    $userMeasurements->chest + $tolerance,
                                ])
                                ->orWhereBetween('gown_bust', [
                                    $userMeasurements->chest - $tolerance,
                                    $userMeasurements->chest + $tolerance,
                                ])
                                ->orWhereBetween('gown_waist', [
                                    $userMeasurements->waist - $tolerance,
                                    $userMeasurements->waist + $tolerance,
                                ])
                                ->orWhereBetween('gown_hips', [
                                    $userMeasurements->hips - $tolerance,
                                    $userMeasurements->hips + $tolerance,
                                ])
                                ->orWhereBetween('gown_shoulder', [
                                    $userMeasurements->shoulder - $tolerance,
                                    $userMeasurements->shoulder + $tolerance,
                                ]);
                        })
                        // ✅ Or check for jacket fits
                        ->orWhere(function ($qq) use ($userMeasurements, $tolerance) {
                            $qq
                                ->whereBetween('jacket_chest', [
                                    $userMeasurements->chest - $tolerance,
                                    $userMeasurements->chest + $tolerance,
                                ])
                                ->orWhereBetween('jacket_waist', [
                                    $userMeasurements->waist - $tolerance,
                                    $userMeasurements->waist + $tolerance,
                                ])
                                ->orWhereBetween('jacket_hip', [
                                    $userMeasurements->hips - $tolerance,
                                    $userMeasurements->hips + $tolerance,
                                ])
                                ->orWhereBetween('jacket_shoulder', [
                                    $userMeasurements->shoulder - $tolerance,
                                    $userMeasurements->shoulder + $tolerance,
                                ]);
                        })
                        // ✅ Or check for trousers
                        ->orWhere(function ($qq) use ($userMeasurements, $tolerance) {
                            $qq
                                ->whereBetween('trouser_waist', [
                                    $userMeasurements->waist - $tolerance,
                                    $userMeasurements->waist + $tolerance,
                                ])
                                ->orWhereBetween('trouser_hip', [
                                    $userMeasurements->hips - $tolerance,
                                    $userMeasurements->hips + $tolerance,
                                ]);
                        });
                });
            }
        }

        $query
            ->orderByRaw("CASE WHEN status = 'Available' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc');

        $products = $query->paginate(12)->appends($request->query());

        if ($request->ajax()) {
            return view('partials.products-grid', compact('products'))->render();
        }

        return view('ProductList', compact('products'));
    }
}
