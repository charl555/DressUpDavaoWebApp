<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class ProductImages extends Model
{
    use HasFactory;

    protected $table = 'product_images';
    protected $primaryKey = 'product_image_id';

    protected $fillable = [
        'product_id',
        'thumbnail_image',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    /**
     * Optimize and convert image to WebP - handles both file objects and stored paths
     */
    public static function optimizeAndConvertToWebP($imageInput, $quality = 85)
    {
        try {
            // If it's a stored file path (string), process the existing file
            if (is_string($imageInput)) {
                $existingPath = $imageInput;

                // Check if file exists
                if (!Storage::disk('public')->exists($existingPath)) {
                    throw new \Exception("File not found: {$existingPath}");
                }

                // Get the full path
                $fullPath = Storage::disk('public')->path($existingPath);
                $image = Image::make($fullPath);

                // Generate new WebP filename
                $filename = pathinfo($existingPath, PATHINFO_FILENAME) . '.webp';
                $newFilePath = 'product-images/' . $filename;
            } else {
                // It's a new file upload (file object)
                $image = Image::make($imageInput->getRealPath());

                // Generate unique filename
                $filename = uniqid() . '.webp';
                $newFilePath = 'product-images/' . $filename;
            }

            // Get original dimensions
            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // Calculate new dimensions (max 2000px on longest side)
            if ($originalWidth > $originalHeight && $originalWidth > 2000) {
                $image->resize(2000, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } elseif ($originalHeight > 2000) {
                $image->resize(null, 2000, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Convert to WebP and save
            $image->encode('webp', $quality);
            Storage::disk('public')->put($newFilePath, $image->stream());

            // Optimize the WebP image
            $fullPath = Storage::disk('public')->path($newFilePath);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($fullPath);

            // If we processed an existing file, delete the old one
            if (is_string($imageInput) && $existingPath !== $newFilePath) {
                Storage::disk('public')->delete($existingPath);
            }

            return $newFilePath;
        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create thumbnail version - handles both file objects and stored paths
     */
    public static function createThumbnail($imageInput, $quality = 80)
    {
        try {
            // If it's a stored file path (string), process the existing file
            if (is_string($imageInput)) {
                $existingPath = $imageInput;

                // Check if file exists
                if (!Storage::disk('public')->exists($existingPath)) {
                    throw new \Exception("File not found: {$existingPath}");
                }

                // Get the full path
                $fullPath = Storage::disk('public')->path($existingPath);
                $image = Image::make($fullPath);

                // Generate new WebP filename
                $filename = 'thumb_' . pathinfo($existingPath, PATHINFO_FILENAME) . '.webp';
                $newFilePath = 'product-images/thumbnails/' . $filename;
            } else {
                // It's a new file upload (file object)
                $image = Image::make($imageInput->getRealPath());

                // Generate unique filename
                $filename = 'thumb_' . uniqid() . '.webp';
                $newFilePath = 'product-images/thumbnails/' . $filename;
            }

            // Resize to thumbnail dimensions (max 500px on longest side)
            $image->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Convert to WebP and save
            $image->encode('webp', $quality);
            Storage::disk('public')->put($newFilePath, $image->stream());

            // Optimize thumbnail
            $fullPath = Storage::disk('public')->path($newFilePath);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($fullPath);

            return $newFilePath;
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process all existing images to convert them to WebP
     */
    public static function convertExistingImagesToWebP()
    {
        $productImages = self::all();

        foreach ($productImages as $productImage) {
            try {
                // Convert thumbnail
                if ($productImage->thumbnail_image) {
                    $newThumbnail = self::optimizeAndConvertToWebP($productImage->thumbnail_image);
                    $productImage->thumbnail_image = $newThumbnail;
                }

                // Convert gallery images
                if ($productImage->images && is_array($productImage->images)) {
                    $newImages = [];
                    foreach ($productImage->images as $imagePath) {
                        $newImages[] = self::optimizeAndConvertToWebP($imagePath);
                    }
                    $productImage->images = $newImages;
                }

                $productImage->save();
            } catch (\Exception $e) {
                \Log::error("Failed to convert images for product image ID {$productImage->product_image_id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Delete associated files when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($productImage) {
            // Delete thumbnail
            if ($productImage->thumbnail_image && Storage::disk('public')->exists($productImage->thumbnail_image)) {
                Storage::disk('public')->delete($productImage->thumbnail_image);
            }

            // Delete gallery images
            if ($productImage->images && is_array($productImage->images)) {
                foreach ($productImage->images as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
            }
        });
    }

    /**
     * Get the full URL for a product image
     */
    public function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        return Storage::disk('public')->url($imagePath);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl()
    {
        return $this->getImageUrl($this->thumbnail_image);
    }

    /**
     * Get gallery image URLs
     */
    public function getGalleryUrls()
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        $urls = [];
        foreach ($this->images as $imagePath) {
            $urls[] = $this->getImageUrl($imagePath);
        }

        return $urls;
    }
}
