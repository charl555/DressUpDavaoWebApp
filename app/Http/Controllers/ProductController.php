<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $recommendationService;

    public function __construct()
    {
        $this->recommendationService = new \App\Services\ProductRecommendationService();
    }

    public function show($product_id)
    {
        $product = Products::with([
            'product_images',
            'user.shop',
            'product_measurements',
            'occasions',
            'product_3d_models',
            'bookings',
            'rentals'
        ])
            ->whereHas('user.shop', function ($query) {
                $query->where('shop_status', 'Verified');
            })
            ->where('product_id', $product_id)
            ->firstOrFail();

        $fitScore = $this->recommendationService->calculateFitScore($product);
        $recommendation = $this->recommendationService->getRecommendationLevel($fitScore);

        $product->fit_score = $fitScore;
        $product->recommendation = $recommendation;

        echo '<script>window.productData = ' . json_encode([
            'id' => $product->product_id,
            'name' => $product->name,
            'shopId' => $product->user->shop->shop_id ?? null,
            'shop' => $product->user->shop->shop_name ?? 'Unknown Shop',
            'owner' => $product->user->name,
            'thumbnail' => $product->product_images->first()->thumbnail_image ?? null
        ]) . ';</script>';

        return view('productoverview', compact('product'));
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
            ->whereHas('user.shop', function ($query) {
                $query->where('shop_status', 'Verified');
            })
            ->with(['product_images', 'events', 'user.shop', 'product_3d_models']);

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

        // Type filter
        if ($request->filled('type')) {
            $types = (array) $request->type;
            $query->whereIn('type', $types);
        }

        // Subtype filter
        if ($request->filled('subtype')) {
            $subtypes = (array) $request->subtype;
            $query->whereIn('subtype', $subtypes);
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

        // Event filter
        if ($request->filled('event')) {
            $events = (array) $request->event;
            $query->whereHas('events', function ($q) use ($events) {
                $q->whereIn('event_name', $events);
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

        // First, get the products without pagination to apply our custom sorting
        $baseQuery = $query->clone();

        // Get all product IDs that match our filters
        $productIds = $baseQuery->pluck('products.product_id')->toArray();

        if (empty($productIds)) {
            $products = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);

            if ($request->ajax()) {
                return view('partials.products-grid', compact('products'))->render();
            }

            return view('productlist', compact('products'));
        }

        // Now create a new query with proper ordering
        $orderedQuery = Products::whereIn('products.product_id', $productIds)
            ->select('products.*')
            ->leftJoin('product_measurements', 'products.product_id', '=', 'product_measurements.product_id')
            ->leftJoin('users', 'products.user_id', '=', 'users.id')
            ->leftJoin('shops', 'users.id', '=', 'shops.user_id')
            // Primary sorting: Availability and measurements
            ->orderByRaw("
            CASE 
                WHEN products.status != 'Available' THEN 3
                WHEN product_measurements.product_id IS NOT NULL THEN 1
                ELSE 2
            END
        ")
            // Secondary sorting: Within same priority, randomize shop order
            ->orderByRaw('RAND(UNIX_TIMESTAMP(NOW()))')
            // Tertiary sorting: Created date for tie-breaking
            ->orderBy('products.created_at', 'desc');

        // Execute the paginated query
        $products = $orderedQuery->paginate(12)->appends($request->query());

        // Calculate fit scores and add recommendation data
        $products->getCollection()->transform(function ($product) {
            $fitScore = $this->recommendationService->calculateFitScore($product);
            $product->fit_score = $fitScore;
            $product->recommendation_level = $this->recommendationService->getRecommendationLevel($fitScore);

            // Check if product has actual measurement values (not just a record)
            $product->has_actual_measurements = $this->hasActualMeasurements($product);

            return $product;
        });

        // IMPORTANT: After calculating fit scores, we need to sort by fit score
        // but we should preserve some randomness to avoid shop clustering
        $sortedCollection = $products->getCollection()->sort(function ($a, $b) {
            // First, prioritize products with actual measurements and higher fit scores
            if ($a->has_actual_measurements && !$b->has_actual_measurements) {
                return -1;
            }

            if (!$a->has_actual_measurements && $b->has_actual_measurements) {
                return 1;
            }

            // If both have measurements, sort by fit score
            if ($a->has_actual_measurements && $b->has_actual_measurements) {
                return $b->fit_score <=> $a->fit_score;  // Descending
            }

            // For products without measurements, maintain random order to mix shops
            // We'll use a deterministic random based on product ID and shop ID
            $aRandom = crc32($a->product_id . $a->user_id) % 100;
            $bRandom = crc32($b->product_id . $b->user_id) % 100;

            return $bRandom <=> $aRandom;  // Sort by "random" value
        });

        // Set the sorted collection back to paginator
        $products->setCollection($sortedCollection);

        if ($request->ajax()) {
            return view('partials.products-grid', compact('products'))->render();
        }

        return view('productlist', compact('products'));
    }

    /**
     * Check if a product has actual measurement values (not just an empty record)
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
