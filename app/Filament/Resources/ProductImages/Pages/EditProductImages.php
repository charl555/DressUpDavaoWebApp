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
        if (isset($data['images']) && is_string($data['images'])) {
            $data['images'] = json_decode($data['images'], true) ?: [];
        }

        return $data;
    }

    public function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Process and save thumbnail
        if (isset($data['thumbnail_image']) && $data['thumbnail_image']) {
            // Check if it's a new file (object) or existing path (string)
            if (is_object($data['thumbnail_image'])) {
                // Delete old thumbnail only if we're uploading a new one
                if ($record->thumbnail_image && Storage::disk('public')->exists($record->thumbnail_image)) {
                    Storage::disk('public')->delete($record->thumbnail_image);
                }

                // Process new thumbnail (file object)
                $record->thumbnail_image = $this->processThumbnailImage($data['thumbnail_image']);
            } else {
                // It's already a file path string, keep it as is
                $record->thumbnail_image = $data['thumbnail_image'];
            }
        }

        // Process and save gallery images
        if (isset($data['images']) && is_array($data['images'])) {
            $newImages = [];

            foreach ($data['images'] as $imageInput) {
                if (is_object($imageInput)) {
                    // It's a new file upload (object)
                    try {
                        $optimizedPath = ProductImages::optimizeAndConvertToWebP($imageInput, 85);
                        $newImages[] = $optimizedPath;
                    } catch (\Exception $e) {
                        \Log::error('Gallery image processing failed: ' . $e->getMessage());
                        $newImages[] = $imageInput->store('product-images', 'public');
                    }
                } else {
                    // It's an existing file path (string)
                    $newImages[] = $imageInput;
                }
            }

            // Delete OLD gallery images only if we're replacing them with new ones
            // But be careful - we only want to delete images that are no longer in the new array
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
