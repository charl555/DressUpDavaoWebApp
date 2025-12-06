<?php

namespace App\Filament\Resources\ProductImages\Pages;

use App\Filament\Resources\ProductImages\ProductImagesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

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
        $this->thumbnail = $this->safeOptimizeAndConvertToWebP($thumbnailInput, 85, 'thumbnails', $record->thumbnail_image);

        $galleryInput = $data['images'] ?? [];
        $this->galleryImages = $this->processGalleryImages($galleryInput, $record->images ?? []);

        unset($data['thumbnail_image'], $data['images']);

        return $data;
    }

    /**
     * Process gallery images with compression and WebP conversion
     * Fixed: Returns empty array when input is empty (not existing images)
     */
    protected function processGalleryImages($galleryInput, $existingImages = [])
    {
        // If input is empty array, user wants to remove all images
        if (empty($galleryInput)) {
            return [];
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
            } else {
                // Use the safe method for new uploads
                $processedPath = $this->safeOptimizeAndConvertToWebP($imageInput, 85, 'gallery');
                if ($processedPath) {
                    $processedImages[] = $processedPath;
                }
            }
        }

        return $processedImages;
    }

    /**
     * Safe method to optimize and convert to WebP with fallback
     * Same as CreateProducts
     */
    protected function safeOptimizeAndConvertToWebP($imageFile, $quality = 85, $type = 'gallery', $existingPath = null)
    {
        // If no new image provided AND it's not an empty array (which means remove), return existing path
        if (!$imageFile) {
            return $existingPath;
        }

        // Handle empty array - user removed the image
        if (is_array($imageFile) && empty($imageFile)) {
            return null;  // Return null to indicate removal
        }

        // Handle array input (from FileUpload with existing files)
        if (is_array($imageFile) && isset($imageFile[0]) && is_string($imageFile[0])) {
            $imagePath = $imageFile[0];
            if (strpos($imagePath, 'http') === 0) {
                $baseUrl = url('storage/');
                $imagePath = str_replace($baseUrl, '', $imagePath);
                $imagePath = ltrim($imagePath, '/');
            }
            return $imagePath;
        }

        // If it's a string (existing path), return as is
        if (is_string($imageFile)) {
            return $imageFile;
        }

        // Check if GD or Imagick is available
        $gdAvailable = extension_loaded('gd') && function_exists('gd_info');
        $imagickAvailable = extension_loaded('imagick');

        Log::info('Image processing check - GD: ' . ($gdAvailable ? 'Yes' : 'No')
            . ', Imagick: ' . ($imagickAvailable ? 'Yes' : 'No'));

        if (!$gdAvailable && !$imagickAvailable) {
            Log::warning('Neither GD nor Imagick is available. Using simple store.');
            $directory = $type === 'thumbnails' ? 'product-images/thumbnails' : 'product-images';
            return $imageFile->store($directory, 'public');
        }

        try {
            // First, store the original file temporarily
            $tempPath = $imageFile->store('temp', 'public');
            $fullTempPath = storage_path('app/public/' . $tempPath);

            // Get file extension
            $extension = strtolower($imageFile->getClientOriginalExtension());

            // Determine output directory
            $directory = $type === 'thumbnails' ? 'product-images/thumbnails' : 'product-images';

            // Generate unique filename with WebP extension
            $filename = uniqid() . '_' . time() . '.webp';
            $outputPath = $directory . '/' . $filename;
            $fullOutputPath = storage_path('app/public/' . $outputPath);

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($directory);

            // Try to process with Intervention Image
            try {
                $image = ImageManagerStatic::make($fullTempPath);

                // Resize if needed (max width 2000px for gallery, 1000px for thumbnails)
                $maxWidth = $type === 'thumbnails' ? 1000 : 2000;
                if ($image->width() > $maxWidth) {
                    $image->resize($maxWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Optimize and convert to WebP
                $image->encode('webp', $quality);

                // Save the processed image
                $image->save($fullOutputPath);

                // Clean up temp file
                Storage::disk('public')->delete($tempPath);

                Log::info('Successfully processed image to WebP: ' . $outputPath);

                // Move thumbnail to correct directory if needed
                if ($type === 'thumbnails' && strpos($outputPath, 'product-images/thumbnails/') !== 0) {
                    $filename = basename($outputPath);
                    $correctPath = 'product-images/thumbnails/' . $filename;

                    if (Storage::disk('public')->exists($outputPath)) {
                        Storage::disk('public')->move($outputPath, $correctPath);
                        $outputPath = $correctPath;
                    }
                }

                return $outputPath;
            } catch (\Exception $e) {
                Log::warning('Intervention Image failed: ' . $e->getMessage());
                Log::warning('Falling back to simple conversion');

                // Fallback: Use GD directly if available
                if ($gdAvailable && in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $this->convertToWebpWithGD($fullTempPath, $fullOutputPath, $quality);

                    // Clean up temp file
                    Storage::disk('public')->delete($tempPath);

                    return $outputPath;
                }

                // Ultimate fallback: just store the original
                Storage::disk('public')->delete($tempPath);
                return $imageFile->store($directory, 'public');
            }
        } catch (\Exception $e) {
            Log::error('Safe optimize failed: ' . $e->getMessage());

            // Ultimate fallback
            $directory = $type === 'thumbnails' ? 'product-images/thumbnails' : 'product-images';
            return $imageFile->store($directory, 'public');
        }
    }

    /**
     * Convert image to WebP using GD as fallback
     */
    protected function convertToWebpWithGD($sourcePath, $destinationPath, $quality = 85)
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            $mimeType = $imageInfo['mime'] ?? '';

            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($sourcePath);
                    // Preserve transparency for PNG
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($sourcePath);
                    break;
                default:
                    throw new \Exception('Unsupported image type: ' . $mimeType);
            }

            // Save as WebP
            imagewebp($image, $destinationPath, $quality);
            imagedestroy($image);
        } catch (\Exception $e) {
            throw new \Exception('GD conversion failed: ' . $e->getMessage());
        }
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        // Update the record with processed images
        // If thumbnail is null (user removed it), set it to null
        $record->thumbnail_image = $this->thumbnail;
        $record->images = $this->galleryImages;
        $record->save();

        // Cleanup old images that are no longer used
        $this->cleanupOldImages();
    }

    /**
     * Clean up old images that are no longer used
     * Fixed: Cleanup even when images are completely removed
     */
    protected function cleanupOldImages(): void
    {
        // Clean up old thumbnail if it was replaced or removed
        if ($this->oldThumbnail &&
                Storage::disk('public')->exists($this->oldThumbnail)) {
            // Delete old thumbnail if:
            // 1. New thumbnail is different, OR
            // 2. New thumbnail is null (user removed it), OR
            // 3. Old thumbnail doesn't match new thumbnail
            if (!$this->thumbnail || $this->oldThumbnail !== $this->thumbnail) {
                Storage::disk('public')->delete($this->oldThumbnail);
            }
        }

        // Clean up old gallery images that are no longer used
        $oldImages = $this->oldGalleryImages ?? [];
        $newImages = $this->galleryImages ?? [];
        $imagesToDelete = array_diff($oldImages, $newImages);

        foreach ($imagesToDelete as $oldImagePath) {
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
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
