<?php

namespace App\Filament\Resources\ProductImages\Pages;

use App\Filament\Resources\ProductImages\ProductImagesResource;
use App\Models\ProductImages as ProductImagesModel;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditProductImages extends EditRecord
{
    protected static string $resource = ProductImagesResource::class;

    protected $thumbnail = null;
    protected $galleryImages = [];
    protected $oldThumbnail = null;
    protected $oldGalleryImages = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->oldThumbnail = $data['thumbnail_image'] ?? null;
        $this->oldGalleryImages = $data['images'] ?? [];

        if (isset($data['images']) && is_string($data['images'])) {
            $data['images'] = json_decode($data['images'], true) ?: [];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        $this->oldThumbnail = $record->thumbnail_image;
        $this->oldGalleryImages = $record->images ?? [];

        $thumbnailInput = $data['thumbnail_image'] ?? null;
        $this->thumbnail = $this->processThumbnailImage($thumbnailInput, $record->thumbnail_image);

        $galleryInput = $data['images'] ?? [];
        $this->galleryImages = $this->processGalleryImages($galleryInput, $record->images ?? []);

        unset($data['thumbnail_image'], $data['images']);

        return $data;
    }

    protected function processThumbnailImage($thumbnailInput, $existingThumbnail = null)
    {
        if (!$thumbnailInput || (is_array($thumbnailInput) && empty($thumbnailInput))) {
            return $existingThumbnail;
        }

        if (is_array($thumbnailInput)) {
            if (!empty($thumbnailInput) && is_string($thumbnailInput[0] ?? null)) {
                $thumbnailPath = $thumbnailInput[0];
                if (strpos($thumbnailPath, 'http') === 0) {
                    $baseUrl = url('storage/');
                    $thumbnailPath = str_replace($baseUrl, '', $thumbnailPath);
                    $thumbnailPath = ltrim($thumbnailPath, '/');
                }
                return $thumbnailPath;
            }
        } elseif (is_object($thumbnailInput)) {
            try {
                $thumbnailPath = ProductImagesModel::optimizeAndConvertToWebP($thumbnailInput, 85);

                if ($thumbnailPath && strpos($thumbnailPath, 'product-images/thumbnails/') !== 0) {
                    $filename = basename($thumbnailPath);
                    $correctPath = 'product-images/thumbnails/' . $filename;

                    if (Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->move($thumbnailPath, $correctPath);
                        $thumbnailPath = $correctPath;
                    }
                }

                return $thumbnailPath;
            } catch (\Exception $e) {
                \Log::error('Thumbnail processing failed: ' . $e->getMessage());

                return $thumbnailInput->store('product-images/thumbnails', 'public');
            }
        } elseif (is_string($thumbnailInput)) {
            return $thumbnailInput;
        }

        return $existingThumbnail;
    }

    protected function processGalleryImages($galleryInput, $existingImages = [])
    {
        if (empty($galleryInput)) {
            return $existingImages;
        }

        $processedImages = [];

        foreach ($galleryInput as $imageInput) {
            if (is_array($imageInput) && isset($imageInput[0]) && is_string($imageInput[0])) {
                $imagePath = $imageInput[0];
                if (strpos($imagePath, 'http') === 0) {
                    $baseUrl = url('storage/');
                    $imagePath = str_replace($baseUrl, '', $imagePath);
                    $imagePath = ltrim($imagePath, '/');
                }
                $processedImages[] = $imagePath;
            } elseif (is_object($imageInput)) {
                try {
                    $optimizedPath = ProductImagesModel::optimizeAndConvertToWebP($imageInput, 85);
                    $processedImages[] = $optimizedPath;
                } catch (\Exception $e) {
                    \Log::error('Gallery image processing failed: ' . $e->getMessage());

                    $processedImages[] = $imageInput->store('product-images', 'public');
                }
            } elseif (is_string($imageInput)) {
                $processedImages[] = $imageInput;
            }
        }

        return $processedImages;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        $record->thumbnail_image = $this->thumbnail;
        $record->images = $this->galleryImages;
        $record->save();

        $this->cleanupOldImages();
    }

    protected function cleanupOldImages(): void
    {
        if ($this->oldThumbnail &&
                $this->thumbnail &&
                $this->oldThumbnail !== $this->thumbnail &&
                Storage::disk('public')->exists($this->oldThumbnail)) {
            Storage::disk('public')->delete($this->oldThumbnail);
        }

        $oldImages = $this->oldGalleryImages ?? [];
        $newImages = $this->galleryImages ?? [];
        $imagesToDelete = array_diff($oldImages, $newImages);

        foreach ($imagesToDelete as $oldImagePath) {
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
