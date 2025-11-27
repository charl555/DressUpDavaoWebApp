<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Products;
use App\Models\Shops;
use Illuminate\Http\Request;

class ShopPageController extends Controller
{
    public function list(Request $request)
    {
        $search = $request->input('search');
        $shops = Shops::with(['user', 'products', 'shop_reviews'])
            ->where('shop_status', '=', 'Verified')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q
                        ->where('shop_name', 'like', "%{$search}%")
                        ->orWhere('shop_address', 'like', "%{$search}%");
                });
            })
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.shop-cards', compact('shops'))->render(),
            ]);
        }

        return view('shops', compact('shops', 'search'));
    }

    public function overview(Request $request, Shops $shop)
    {
        $query = Products::where('visibility', 'Yes')
            ->where('user_id', $shop->user_id)
            ->with(['product_images', 'occasions', 'user.shop']);

        // Gender-based category filtering
        $userGender = auth()->check() ? auth()->user()->gender : null;

        if ($userGender === 'Female') {
            // Show only Gowns and Dresses for female users
            $query->whereIn('type', ['Gown', 'Dress']);
        } elseif ($userGender === 'Male') {
            // Show only Suits and Jackets for male users
            $query->whereIn('type', ['Suit', 'Jacket']);
        }
        // For guests or users with other/prefer not to say gender, show all products

        // Subtype filter
        if ($request->filled('subtype')) {
            $query->whereIn('subtype', (array) $request->subtype);
        }

        // Size filter
        if ($request->filled('size')) {
            $sizeMapping = [
                // Individual Sizes
                'XS' => 'XS',
                'S' => 'S',
                'M' => 'M',
                'L' => 'L',
                'XL' => 'XL',
                'XXL' => 'XXL',
                'XXXL' => 'XXXL',
                // --- Most Common Ranges ---
                'XS-S' => 'XS-S',
                'S-M' => 'S-M',
                'M-L' => 'M-L',
                'L-XL' => 'L-XL',
                'XL-XXL' => 'XL-XXL',
                // --- Extended Ranges ---
                'XXS-S' => 'XXS-S',
                'XS-M' => 'XS-M',
                'S-L' => 'S-L',
                'M-XL' => 'M-XL',
                'L-XXL' => 'L-XXL',
                'XXS-M' => 'XXS-M',
                'XS-L' => 'XS-L',
                'S-XL' => 'S-XL',
                'M-XXL' => 'M-XXL',
                // --- Broad Ranges ---
                'XXS-L' => 'XXS-L',
                'XS-XL' => 'XS-XL',
                'S-XXL' => 'S-XXL',
                'XXS-XL' => 'XXS-XL',
                'XS-XXL' => 'XS-XXL',
                'Adjustable' => 'Adjustable/Customizable',
            ];

            $sizes = (array) $request->size;
            $query->where(function ($q) use ($sizes, $sizeMapping) {
                foreach ($sizes as $size) {
                    if (isset($sizeMapping[$size])) {
                        $dbSize = $sizeMapping[$size];
                        $q->orWhere('size', $dbSize);
                    } else {
                        // Fallback for any unmapped sizes
                        $q->orWhere('size', 'LIKE', "%$size%");
                    }
                }
            });
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
                foreach ($occasions as $occasion) {
                    $q->orWhereJsonContains('occasion_name', $occasion);
                }
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

        $reviews = $shop
            ->shop_reviews()
            ->with('user')
            ->latest()
            ->get()
            ->map(function ($review) use ($shop) {
                $review->booking_count = Bookings::where('user_id', $review->user_id)
                    ->whereHas('product.user.shop', fn($q) => $q->where('shop_id', $shop->shop_id))
                    ->count();
                return $review;
            });

        $averageRating = $reviews->isNotEmpty()
            ? round($reviews->avg('rating'), 1)
            : 0;

        if ($request->ajax()) {
            return view('partials.products-grid', compact('products'))->render();
        }

        return view('shopoverview', compact('shop', 'products', 'reviews', 'averageRating'));
    }
}
