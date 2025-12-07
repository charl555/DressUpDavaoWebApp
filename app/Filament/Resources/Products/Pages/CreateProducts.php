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

    protected $eventsData = [];
    protected $measurementsData = [];
    protected $thumbnail = null;
    protected $galleryImages = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        // Extract events from product_events field (now it's an array from the select field)
        $this->eventsData = $this->processEventsData($data['product_events'] ?? []);
        unset($data['product_events']);

        // Always extract measurement fields, even if empty
        $this->measurementsData = Arr::only($data, [
            'gown_neck',
            'gown_shoulder',
            'gown_back_width',
            'gown_bust',
            'gown_chest',
            'gown_bust_point',
            'gown_bust_distance',
            'gown_arm_hole',
            'gown_waist',
            'gown_hips',
            'gown_figure',
            'gown_sleeve_width',
            'gown_length',
            'jacket_shoulder',
            'jacket_back_width',
            'jacket_chest',
            'jacket_bust',
            'jacket_arm_hole',
            'jacket_sleeve_length',
            'jacket_waist',
            'jacket_hip',
            'jacket_figure',
            'jacket_sleeve_width',
            'jacket_bicep',
            'jacket_length',
            'trouser_waist',
            'trouser_hip',
            'trouser_crotch',
            'trouser_thigh',
            'trouser_knee',
            'trouser_bottom',
            'trouser_leg_opening',
            'trouser_inseam',
            'trouser_outseam',
            'trouser_length',
        ]);
        $data = Arr::except($data, array_keys($this->measurementsData));

        // Handle images with compression and WebP conversion
        $this->thumbnail = $this->processThumbnailImage($data['thumbnail_image'] ?? null);
        $this->galleryImages = $this->processGalleryImages($data['images'] ?? []);

        unset($data['thumbnail_image'], $data['images']);

        return $data;
    }

    /**
     * Process events data from array (now comes directly from the select field)
     */
    protected function processEventsData($eventsInput): array
    {
        if (empty($eventsInput)) {
            return [];
        }

        // If it's already an array (from the select field), just clean it
        if (is_array($eventsInput)) {
            $events = array_map(function ($event) {
                return trim($event);
            }, $eventsInput);

            // Remove empty values and duplicates
            return array_values(array_unique(array_filter($events)));
        }

        // Fallback for string input (shouldn't happen with the new form, but just in case)
        if (is_string($eventsInput) && !empty($eventsInput)) {
            $events = array_map(function ($event) {
                return trim($event);
            }, explode(',', $eventsInput));

            // Remove empty values and duplicates
            return array_values(array_unique(array_filter($events)));
        }

        return [];
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
            $thumbnailPath = ProductImages::optimizeAndConvertToWebP($thumbnailFile, 85);

            // Ensure the path includes the thumbnails directory
            if ($thumbnailPath && strpos($thumbnailPath, 'product-images/thumbnails/') !== 0) {
                // Move to correct directory if needed
                $filename = basename($thumbnailPath);
                $correctPath = 'product-images/thumbnails/' . $filename;

                if (\Storage::disk('public')->exists($thumbnailPath)) {
                    \Storage::disk('public')->move($thumbnailPath, $correctPath);
                    $thumbnailPath = $correctPath;
                }
            }

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Thumbnail processing failed: ' . $e->getMessage());
            // Fallback with correct directory
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

        // Create events from processed data - each as a separate record
        foreach ($this->eventsData as $eventName) {
            if (!empty($eventName)) {
                $product->events()->create([
                    'event_name' => $eventName,
                ]);
            }
        }

        // ALWAYS create a measurements record, even with empty data
        $product->product_measurements()->create(
            array_merge($this->measurementsData, ['product_id' => $product->product_id])
        );

        // Create product images record
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
