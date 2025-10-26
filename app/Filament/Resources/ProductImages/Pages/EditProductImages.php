<?php

namespace App\Filament\Resources\ProductImages\Pages;

use App\Filament\Resources\ProductImages\ProductImagesResource;
use App\Models\ProductImages;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditProductImages extends EditRecord
{
    protected static string $resource = ProductImagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Only handle JSON decoding for images array, don't convert to URLs
        if (isset($data['images']) && is_string($data['images'])) {
            $data['images'] = json_decode($data['images'], true) ?: [];
        }

        return $data;
    }

    public function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Process thumbnail image
        if (isset($data['thumbnail_image'])) {
            if (is_array($data['thumbnail_image'])) {
                // It's from FileUpload with existing files
                $thumbnailData = $data['thumbnail_image'];

                if (!empty($thumbnailData) && is_string($thumbnailData[0] ?? null)) {
                    // Extract path from URL if it's a full URL
                    $thumbnailPath = $thumbnailData[0];
                    if (strpos($thumbnailPath, 'http') === 0) {
                        $baseUrl = asset('storage/');
                        $thumbnailPath = str_replace($baseUrl, '', $thumbnailPath);
                        $thumbnailPath = ltrim($thumbnailPath, '/');
                    }
                    $record->thumbnail_image = $thumbnailPath;
                }
            } elseif (is_object($data['thumbnail_image'])) {
                // It's a new file upload
                if ($record->thumbnail_image && Storage::disk('public')->exists($record->thumbnail_image)) {
                    Storage::disk('public')->delete($record->thumbnail_image);
                }
                $record->thumbnail_image = $this->processThumbnailImage($data['thumbnail_image']);
            } elseif (is_string($data['thumbnail_image'])) {
                // It's already a file path
                $record->thumbnail_image = $data['thumbnail_image'];
            }
        }

        // Process gallery images
        if (isset($data['images']) && is_array($data['images'])) {
            $newImages = [];

            foreach ($data['images'] as $imageInput) {
                if (is_array($imageInput) && isset($imageInput[0]) && is_string($imageInput[0])) {
                    // It's from FileUpload with existing files
                    $imagePath = $imageInput[0];
                    if (strpos($imagePath, 'http') === 0) {
                        $baseUrl = asset('storage/');
                        $imagePath = str_replace($baseUrl, '', $imagePath);
                        $imagePath = ltrim($imagePath, '/');
                    }
                    $newImages[] = $imagePath;
                } elseif (is_object($imageInput)) {
                    // It's a new file upload
                    try {
                        $optimizedPath = ProductImages::optimizeAndConvertToWebP($imageInput, 85);
                        $newImages[] = $optimizedPath;
                    } catch (\Exception $e) {
                        \Log::error('Gallery image processing failed: ' . $e->getMessage());
                        $newImages[] = $imageInput->store('product-images', 'public');
                    }
                } elseif (is_string($imageInput)) {
                    // It's an existing file path
                    $newImages[] = $imageInput;
                }
            }

            // Clean up old images that are no longer used
            $oldImages = $record->images ?? [];
            $imagesToDelete = array_diff($oldImages, $newImages);

            foreach ($imagesToDelete as $oldImagePath) {
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            $record->images = $newImages;
        }

        $record->save();

        return $record;
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
            return ProductImages::createThumbnail($thumbnailFile, 80);
        } catch (\Exception $e) {
            \Log::error('Thumbnail processing failed: ' . $e->getMessage());
            return $thumbnailFile->store('product-images/thumbnails', 'public');
        }
    }
}
