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

        // Calculate fit scores BEFORE pagination for consistent sorting
        // First, get all filtered product IDs
        $filteredProductIds = $query->pluck('products.product_id')->toArray();

        if (empty($filteredProductIds)) {
            $products = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);

            if ($request->ajax()) {
                return view('partials.products-grid', compact('products'))->render();
            }

            return view('productlist', compact('products'));
        }

        // Get ALL products with their fit scores in a single query
        $allProducts = Products::whereIn('products.product_id', $filteredProductIds)
            ->with(['product_images', 'events', 'user.shop', 'product_3d_models', 'product_measurements'])
            ->get()
            ->map(function ($product) {
                $fitScore = $this->recommendationService->calculateFitScore($product);
                $product->fit_score = $fitScore;
                $product->recommendation_level = $this->recommendationService->getRecommendationLevel($fitScore);
                $product->has_actual_measurements = $this->hasActualMeasurements($product);
                return $product;
            })
            ->sort(function ($a, $b) {
                // Primary: Products with measurements first
                if ($a->has_actual_measurements && !$b->has_actual_measurements) {
                    return -1;
                }
                if (!$a->has_actual_measurements && $b->has_actual_measurements) {
                    return 1;
                }

                // Secondary: Sort by fit score (descending) for products with measurements
                if ($a->has_actual_measurements && $b->has_actual_measurements) {
                    $scoreComparison = $b->fit_score <=> $a->fit_score;
                    if ($scoreComparison !== 0) {
                        return $scoreComparison;
                    }
                }

                // Tertiary: For products without measurements or with equal fit scores,
                // use a deterministic order based on product_id and user_id
                $aHash = crc32($a->product_id . $a->user_id . $a->created_at);
                $bHash = crc32($b->product_id . $b->user_id . $b->created_at);

                // If still equal, use created_at as final tie-breaker
                if ($aHash === $bHash) {
                    return $b->created_at <=> $a->created_at;
                }

                return $bHash <=> $aHash;
            })
            ->values();  // Reset keys to maintain order

        // Create a consistent seed for randomization based on filters
        $filterString = json_encode($request->all());
        $seed = crc32($filterString);

        // Use the seed to shuffle products deterministically
        mt_srand($seed);
        $shuffledProducts = $allProducts->shuffle(mt_rand());
        mt_srand();  // Reset seed

        // Now paginate the already sorted collection
        $page = $request->input('page', 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $paginatedProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $shuffledProducts->slice($offset, $perPage)->values(),
            $shuffledProducts->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $products = $paginatedProducts;

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
