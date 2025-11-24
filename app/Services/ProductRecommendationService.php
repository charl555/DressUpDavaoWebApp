<?php

namespace App\Services;

use App\Models\Products;
use App\Models\UserMeasurements;
use Illuminate\Support\Facades\Auth;

class ProductRecommendationService
{
    private $userMeasurements;
    private $tolerance = 2;  // inches

    public function __construct()
    {
        if (Auth::check()) {
            $this->userMeasurements = UserMeasurements::where('user_id', Auth::id())->first();
        }
    }

    public function calculateFitScore($product)
    {
        if (!$this->userMeasurements) {
            return 0;
        }

        $productMeasurements = $product->product_measurements;
        if (!$productMeasurements) {
            return 0;
        }

        $score = 0;
        $totalPossibleScore = 0;

        // Define measurement mappings based on product type
        if ($product->type === 'Gown') {
            $measurementMappings = [
                ['user' => 'chest', 'product' => 'gown_chest', 'weight' => 1.2],
                ['user' => 'chest', 'product' => 'gown_bust', 'weight' => 1.2],
                ['user' => 'waist', 'product' => 'gown_waist', 'weight' => 1.5],
                ['user' => 'hips', 'product' => 'gown_hips', 'weight' => 1.3],
                ['user' => 'shoulder', 'product' => 'gown_shoulder', 'weight' => 1.0],
            ];
        } elseif ($product->type === 'Suit') {
            $measurementMappings = [
                ['user' => 'chest', 'product' => 'jacket_chest', 'weight' => 1.4],
                ['user' => 'waist', 'product' => 'jacket_waist', 'weight' => 1.3],
                ['user' => 'hips', 'product' => 'jacket_hip', 'weight' => 1.2],
                ['user' => 'shoulder', 'product' => 'jacket_shoulder', 'weight' => 1.1],
                ['user' => 'waist', 'product' => 'trouser_waist', 'weight' => 1.3],
                ['user' => 'hips', 'product' => 'trouser_hip', 'weight' => 1.2],
            ];
        } else {
            return 0;
        }

        foreach ($measurementMappings as $mapping) {
            $userValue = $this->userMeasurements->{$mapping['user']};
            $productValue = $productMeasurements->{$mapping['product']};

            if ($userValue && $productValue) {
                $difference = abs($userValue - $productValue);
                $measurementScore = max(0, 100 - ($difference * 20));  // Convert difference to score
                $weightedScore = $measurementScore * $mapping['weight'];

                $score += $weightedScore;
                $totalPossibleScore += 100 * $mapping['weight'];
            }
        }

        if ($totalPossibleScore === 0) {
            return 0;
        }

        return ($score / $totalPossibleScore) * 100;
    }

    public function getRecommendationLevel($fitScore)
    {
        if ($fitScore >= 90) {
            return [
                'level' => 'perfect',
                'label' => 'Perfect Fit',
                'description' => 'This product matches your measurements closely and will likely fit perfectly.',
                'color' => 'emerald',
            ];
        } elseif ($fitScore >= 75) {
            return [
                'level' => 'excellent',
                'label' => 'Excellent Fit',
                'description' => 'This product closely matches your measurements and should fit very well.',
                'color' => 'green',
            ];
        } elseif ($fitScore >= 60) {
            return [
                'level' => 'good',
                'label' => 'Good Fit',
                'description' => 'This product matches most of your measurements and should fit well.',
                'color' => 'blue',
            ];
        } elseif ($fitScore >= 40) {
            return [
                'level' => 'fair',
                'label' => 'Fair Fit',
                'description' => 'This product may require minor adjustments to fit perfectly.',
                'color' => 'yellow',
            ];
        } else {
            return [
                'level' => 'poor',
                'label' => 'May Not Fit',
                'description' => 'This product may not fit well based on your measurements.',
                'color' => 'red',
            ];
        }
    }

    public function addRecommendationData($products)
    {
        if (!$this->userMeasurements) {
            return $products->map(function ($product) {
                $product->recommendation_level = null;
                $product->fit_score = 0;
                return $product;
            });
        }

        return $products->map(function ($product) {
            $fitScore = $this->calculateFitScore($product);
            $product->fit_score = $fitScore;
            $product->recommendation_level = $this->getRecommendationLevel($fitScore);
            return $product;
        });
    }

    public function sortByRecommendation($products)
    {
        return $products->sortByDesc('fit_score');
    }

    private function hasActualMeasurements($productMeasurements)
    {
        $measurementFields = [
            'gown_chest', 'gown_bust', 'gown_waist', 'gown_hips', 'gown_shoulder',
            'jacket_chest', 'jacket_waist', 'jacket_hip', 'jacket_shoulder',
            'trouser_waist', 'trouser_hip'
        ];

        foreach ($measurementFields as $field) {
            if (!empty($productMeasurements->$field) && $productMeasurements->$field > 0) {
                return true;
            }
        }

        return false;
    }
}
