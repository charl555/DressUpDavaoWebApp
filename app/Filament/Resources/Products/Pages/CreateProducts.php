<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductsResource;
use App\Models\ProductImages;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;

    protected $occasionsData = [];
    protected $measurementsData = [];
    protected $thumbnail = null;
    protected $galleryImages = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $this->occasionsData = $data['occasion_names'] ?? [];
        unset($data['occasion_names']);

        $this->measurementsData = Arr::only($data, [
            'gown_length',
            'gown_chest',
            'gown_waist',
            'gown_hips',
            'jacket_chest',
            'jacket_length',
            'jacket_shoulder',
            'jacket_sleeve_length',
            'jacket_sleeve_width',
            'jacket_bicep',
            'jacket_arm_hole',
            'jacket_waist',
            'trouser_waist',
            'trouser_hip',
            'trouser_inseam',
            'trouser_outseam',
            'trouser_thigh',
            'trouser_leg_opening',
            'trouser_crotch',
        ]);
        $data = Arr::except($data, array_keys($this->measurementsData));

        // Handle images with compression and WebP conversion
        $this->thumbnail = $this->processThumbnailImage($data['thumbnail_image'] ?? null);
        $this->galleryImages = $this->processGalleryImages($data['images'] ?? []);

        unset($data['thumbnail_image'], $data['images']);

        return $data;
    }

    /**
     * Process thumbnail image with compression and WebP conversion
     */
    protected function processThumbnailImage($thumbnailFile)
    {
        if (!$thumbnailFile) {
            return null;
        }

        try {
            // For new uploads, it's a file object; for edits, it might be a string path
            return ProductImages::createThumbnail($thumbnailFile, 80);
        } catch (\Exception $e) {
            \Log::error('Thumbnail processing failed: ' . $e->getMessage());
            // Fallback: store original in your existing directory
            if (is_string($thumbnailFile)) {
                return $thumbnailFile;  // Keep existing path
            } else {
                return $thumbnailFile->store('product-images/thumbnails', 'public');
            }
        }
    }

    /**
     * Process gallery images with compression and WebP conversion
     */
    protected function processGalleryImages($galleryFiles)
    {
        if (empty($galleryFiles)) {
            return [];
        }

        $processedImages = [];

        foreach ($galleryFiles as $imageFile) {
            try {
                // For new uploads, it's a file object; for edits, it might be a string path
                $optimizedPath = ProductImages::optimizeAndConvertToWebP($imageFile, 85);
                $processedImages[] = $optimizedPath;
            } catch (\Exception $e) {
                \Log::error('Gallery image processing failed: ' . $e->getMessage());
                // Fallback to original
                if (is_string($imageFile)) {
                    $processedImages[] = $imageFile;  // Keep existing path
                } else {
                    $processedImages[] = $imageFile->store('product-images', 'public');
                }
            }
        }

        return $processedImages;
    }

    protected function afterCreate(): void
    {
        $product = $this->record;

        foreach ($this->occasionsData as $occasion_name) {
            $product->occasions()->create([
                'occasion_name' => $occasion_name,
            ]);
        }

        if (!empty($this->measurementsData)) {
            $product->product_measurements()->create(
                array_merge($this->measurementsData, ['product_id' => $product->product_id])
            );
        }

        $product->product_images()->create([
            'thumbnail_image' => $this->thumbnail,
            'images' => $this->galleryImages,
        ]);

        Notification::make()
            ->title('New Product Created')
            ->body("A new product named '{$product->name}' was created.")
            ->success()
            ->sendToDatabase(Auth::user());
    }
}
