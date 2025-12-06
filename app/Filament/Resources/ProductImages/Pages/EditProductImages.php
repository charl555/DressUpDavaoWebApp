<?php

namespace App\Filament\Resources\ProductImages\Pages;

use App\Filament\Resources\ProductImages\ProductImagesResource;
use App\Models\ProductImages as ProductImagesModel;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditProductImages extends EditRecord
{
    protected static string $resource = ProductImagesResource::class;

    protected $thumbnail = null;
    protected $galleryImages = [];
    protected $oldThumbnail = null;
    protected $oldGalleryImages = [];

    protected function convertFilamentFileArrayToUploadedFile($fileArray)
    {
        if (!is_array($fileArray) || !isset($fileArray['path'])) {
            return null;
        }

        $storagePath = storage_path('app/public/' . $fileArray['path']);

        if (!file_exists($storagePath)) {
            return null;
        }

        return new UploadedFile(
            $storagePath,
            $fileArray['name'] ?? basename($storagePath),
            $fileArray['type'] ?? null,
            null,
            true  // mark as test mode
        );
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->oldThumbnail = $data['thumbnail_image'] ?? null;

        if (!empty($data['images']) && is_string($data['images'])) {
            $this->oldGalleryImages = json_decode($data['images'], true) ?? [];
        } else {
            $this->oldGalleryImages = $data['images'] ?? [];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        $this->oldThumbnail = $record->thumbnail_image;
        $this->oldGalleryImages = $record->images ?? [];

        // THUMBNAIL
        $this->thumbnail = $this->processThumbnailImage($data['thumbnail_image'] ?? null);

        // GALLERY IMAGES
        $this->galleryImages = $this->processGalleryImages($data['images'] ?? []);

        unset($data['thumbnail_image'], $data['images']);

        return $data;
    }

    protected function processThumbnailImage($thumbnailInput)
    {
        // If no new input → keep old
        if ($thumbnailInput === null) {
            return $this->oldThumbnail;
        }

        // User removed thumbnail (FileUpload returns empty array)
        if (is_array($thumbnailInput) && empty($thumbnailInput)) {
            return null;
        }

        // Existing file path
        if (is_string($thumbnailInput)) {
            return $thumbnailInput;
        }

        // NEW UPLOADED FILE FROM FILAMENT
        if (is_array($thumbnailInput) && isset($thumbnailInput[0])) {
            $uploadedFile = $this->convertFilamentFileArrayToUploadedFile($thumbnailInput[0]);

            if ($uploadedFile instanceof UploadedFile) {
                try {
                    $path = ProductImagesModel::optimizeAndConvertToWebP($uploadedFile, 85);

                    // move to correct directory if needed
                    if (!str_starts_with($path, 'product-images/thumbnails/')) {
                        $filename = basename($path);
                        $newPath = 'product-images/thumbnails/' . $filename;

                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->move($path, $newPath);
                        }

                        return $newPath;
                    }

                    return $path;
                } catch (\Exception $e) {
                    Log::error('Thumbnail conversion failed: ' . $e->getMessage());
                    return $uploadedFile->store('product-images/thumbnails', 'public');
                }
            }
        }

        return $this->oldThumbnail;
    }

    protected function processGalleryImages($galleryInput)
    {
        if (empty($galleryInput)) {
            return [];
        }

        $processed = [];

        foreach ($galleryInput as $imageInput) {
            // Existing stored file
            if (is_string($imageInput)) {
                $processed[] = $imageInput;
                continue;
            }

            // New upload array from Filament
            if (is_array($imageInput) && isset($imageInput[0])) {
                $uploadedFile = $this->convertFilamentFileArrayToUploadedFile($imageInput[0]);

                if ($uploadedFile instanceof UploadedFile) {
                    try {
                        $path = ProductImagesModel::optimizeAndConvertToWebP($uploadedFile, 85);
                        $processed[] = $path;
                        continue;
                    } catch (\Exception $e) {
                        Log::error('Gallery conversion failed: ' . $e->getMessage());
                        $processed[] = $uploadedFile->store('product-images', 'public');
                        continue;
                    }
                }
            }
        }

        return $processed;
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
        // OLD THUMBNAIL
        if ($this->oldThumbnail && $this->oldThumbnail !== $this->thumbnail) {
            if (Storage::disk('public')->exists($this->oldThumbnail)) {
                Storage::disk('public')->delete($this->oldThumbnail);
            }
        }

        // OLD GALLERY
        $removed = array_diff($this->oldGalleryImages, $this->galleryImages);

        foreach ($removed as $img) {
            if (Storage::disk('public')->exists($img)) {
                Storage::disk('public')->delete($img);
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
