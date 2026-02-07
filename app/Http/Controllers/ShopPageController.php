<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Products;
use App\Models\Shops;
use Illuminate\Http\Request;

class ShopPageController extends Controller
{
    private $recommendationService;

    public function __construct()
    {
        $this->recommendationService = new \App\Services\ProductRecommendationService();
    }

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

        // Check if it's a mobile request
        $isMobileApp = $request->has('app') ||
            $request->has('mobile_nav') ||
            str_contains($request->header('User-Agent'), 'DressUpDavaoApp');

        // If AJAX request for desktop (keep for backward compatibility)
        if ($request->ajax() && !$isMobileApp) {
            return response()->json([
                'html' => view('partials.shop-cards', compact('shops'))->render(),
            ]);
        }

        return view('shops', compact('shops', 'search', 'isMobileApp'));
    }

    public function overview(Request $request, Shops $shop)
    {
        $query = Products::where('visibility', 'Yes')
            ->where('user_id', $shop->user_id)
            ->with(['product_images', 'occasions', 'user.shop', 'product_measurements']);

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
            $sizes = (array) $request->size;

            $query->where(function ($q) use ($sizes) {
                foreach ($sizes as $size) {
                    // If it's an individual size (XS, S, M, L, XL, XXL, XXXL)
                    if (in_array($size, ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'])) {
                        $q->orWhere(function ($subQ) use ($size) {
                            // Match exact size
                            $subQ->where('size', $size);

                            // Match size ranges that include this size
                            $subQ->orWhere('size', 'LIKE', "%-{$size}");
                            $subQ->orWhere('size', 'LIKE', "{$size}-%");
                            $subQ->orWhere('size', 'LIKE', "%-{$size}-%");

                            // Match specific range patterns for this size
                            $rangePatterns = [
                                "XS-{$size}", "S-{$size}", "M-{$size}", "L-{$size}", "XL-{$size}",
                                "XXS-{$size}", "XXS-{$size}", "{$size}-S", "{$size}-M", "{$size}-L",
                                "{$size}-XL", "{$size}-XXL", "XXS-{$size}", "XXS-{$size}"
                            ];

                            foreach ($rangePatterns as $pattern) {
                                $subQ->orWhere('size', $pattern);
                            }
                        });
                    }
                    // If it's a size range
                    else {
                        // Extract start and end sizes from the range
                        if (strpos($size, '-') !== false) {
                            list($startSize, $endSize) = explode('-', $size);

                            $q->orWhere(function ($subQ) use ($size, $startSize, $endSize) {
                                // Match exact range
                                $subQ->where('size', $size);

                                // Match ranges that overlap with this range
                                $subQ->orWhere('size', 'LIKE', "{$startSize}-%");
                                $subQ->orWhere('size', 'LIKE', "%-{$endSize}");

                                // For adjustable sizes
                                if ($size === 'Adjustable') {
                                    $subQ->orWhere('size', 'LIKE', '%Adjustable%');
                                    $subQ->orWhere('size', 'LIKE', '%Customizable%');
                                }
                            });
                        }
                        // For adjustable/customizable
                        elseif ($size === 'Adjustable') {
                            $q->orWhere('size', 'LIKE', '%Adjustable%');
                            $q->orWhere('size', 'LIKE', '%Customizable%');
                        }
                        // Fallback for other sizes
                        else {
                            $q->orWhere('size', 'LIKE', "%{$size}%");
                        }
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

        // Use the same ordering logic as productlist
        $query
            ->select('products.*')
            ->leftJoin('product_measurements', 'products.product_id', '=', 'product_measurements.product_id')
            ->orderByRaw("
            CASE 
                WHEN products.status != 'Available' THEN 3
                WHEN product_measurements.product_id IS NOT NULL THEN 1
                ELSE 2
            END
        ")
            ->orderByRaw("
            CASE 
                WHEN product_measurements.product_id IS NOT NULL THEN 
                    CASE 
                        WHEN products.status = 'Available' THEN 0 
                        ELSE 1 
                    END
                ELSE 0
            END
        ")
            ->orderBy('products.created_at', 'desc');

        $products = $query->paginate(12)->appends($request->query());

        // Calculate fit scores and add recommendation data (same as productlist)
        $products->getCollection()->transform(function ($product) {
            $fitScore = $this->recommendationService->calculateFitScore($product);
            $product->fit_score = $fitScore;
            $product->recommendation_level = $this->recommendationService->getRecommendationLevel($fitScore);

            // Check if product has actual measurement values (not just a record)
            $product->has_actual_measurements = $this->hasActualMeasurements($product);

            return $product;
        });

        // Sort the collection by fit score for products with measurements (same as productlist)
        $sortedCollection = $products->getCollection()->sortByDesc(function ($product) {
            // Products with actual measurements sorted by fit score, others maintain their order
            return $product->has_actual_measurements ? $product->fit_score : 0;
        });

        // Set the sorted collection back to paginator
        $products->setCollection($sortedCollection);

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

    /**
     * Check if a product has actual measurement values (not just an empty record)
     * Same method as in ProductController
     */
    private function hasActualMeasurements($product)
    {
        if (!$product->product_measurements) {
            return false;
        }

        $measurements = $product->product_measurements;

        // Define all measurement fields to check
        $measurementFields = [
            // Gown measurements
            'gown_chest', 'gown_bust', 'gown_waist', 'gown_hips', 'gown_shoulder', 'gown_length', 'gown_upper_chest',
            // Jacket measurements
            'jacket_chest', 'jacket_waist', 'jacket_hip', 'jacket_shoulder', 'jacket_length', 'jacket_sleeve_length',
            'jacket_sleeve_width', 'jacket_bicep', 'jacket_arm_hole',
            // Trouser measurements
            'trouser_waist', 'trouser_hip', 'trouser_inseam', 'trouser_outseam', 'trouser_thigh', 'trouser_leg_opening', 'trouser_crotch'
        ];

        // Check if any measurement field has a non-null, non-empty value
        foreach ($measurementFields as $field) {
            if (!empty($measurements->$field) && $measurements->$field > 0) {
                return true;
            }
        }

        return false;
    }
}
