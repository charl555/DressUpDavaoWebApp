<?php

namespace App\Services;

use App\Models\KiriEngineJobs;
use App\Models\Stored3dModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use ZipArchive;

class ModelDownloadService
{
    public function downloadAndStoreModel(KiriEngineJobs $job): ?Stored3dModels
    {
        try {
            Log::info('Starting download and store process', ['job_id' => $job->kiri_engine_job_id]);

            // Get download URL
            $downloadUrl = $this->getDownloadUrl($job);

            if (!$downloadUrl) {
                throw new Exception('Could not get download URL from Kiri Engine');
            }

            Log::info('Download URL obtained', ['url' => $downloadUrl]);

            // Download the zip file
            $zipContent = $this->downloadZipFile($downloadUrl);

            if (!$zipContent) {
                throw new Exception('Failed to download zip file from URL');
            }

            Log::info('Zip file downloaded', ['size' => strlen($zipContent)]);

            // Save zip file temporarily and get the actual path
            $tempZipPath = $this->saveTempZip($zipContent, $job->serialize_id);

            // Verify the file exists before extraction
            if (!file_exists($tempZipPath)) {
                throw new Exception("Temp zip file does not exist at: {$tempZipPath}");
            }

            Log::info('Temp zip file verified', ['path' => $tempZipPath, 'file_size' => filesize($tempZipPath)]);

            // Extract zip file
            $extractedPath = $this->extractZipFile($tempZipPath, $job->serialize_id);

            // Store in database
            $storedModel = $this->storeModelInDatabase($job, $extractedPath);

            // Clean up temp files
            $this->cleanupTempFiles($tempZipPath);

            Log::info('3D model stored successfully', ['stored_model_id' => $storedModel->stored_3d_model_id]);

            return $storedModel;
        } catch (Exception $e) {
            Log::error('Failed to download and store 3D model', [
                'job_id' => $job->kiri_engine_job_id,
                'serialize_id' => $job->serialize_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Auto-attach stored 3D model to product
     */
    private function autoAttachToProduct(KiriEngineJobs $job, Stored3dModels $storedModel): void
    {
        try {
            // Check if job has a product association
            if (!$job->product_id) {
                Log::info('No product_id found for auto-attach', [
                    'job_id' => $job->kiri_engine_job_id
                ]);
                return;
            }

            // Get the product
            $product = Products::find($job->product_id);
            if (!$product) {
                Log::warning('Product not found for auto-attach', [
                    'job_id' => $job->kiri_engine_job_id,
                    'product_id' => $job->product_id
                ]);
                return;
            }

            // Use the exact same model_path from stored3d_models
            $modelPath = $storedModel->model_path;

            // Check if a 3D model already exists for this product
            $existingModel = $product->product_3d_models;

            if ($existingModel) {
                // Update existing model_path with the exact same path
                $existingModel->update(['model_path' => $modelPath]);
                $message = "3D model automatically updated for '{$product->name}'!";
                $logAction = 'updated';
            } else {
                // Create a new 3D model record with the exact same path
                Product3dModels::create([
                    'product_id' => $product->product_id,
                    'model_path' => $modelPath,
                ]);
                $message = "3D model automatically attached to '{$product->name}'!";
                $logAction = 'attached';
            }

            // Send notification (if in web context)
            if (app()->runningInConsole() === false) {
                try {
                    Notification::make()
                        ->title('3D Model Auto-Attached')
                        ->body($message)
                        ->success()
                        ->send();
                } catch (Exception $e) {
                    Log::warning('Failed to send notification', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info("3D model auto-{$logAction} to product", [
                'job_id' => $job->kiri_engine_job_id,
                'product_id' => $job->product_id,
                'product_name' => $product->name,
                'model_path' => $modelPath,
                'stored_model_id' => $storedModel->stored_3d_model_id
            ]);
        } catch (Exception $e) {
            Log::error('Failed to auto-attach 3D model to product', [
                'job_id' => $job->kiri_engine_job_id,
                'product_id' => $job->product_id,
                'stored_model_id' => $storedModel->stored_3d_model_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Download, store, and auto-attach to product if applicable
     * This is a convenience method that combines both operations
     */
    public function downloadStoreAndAttach(KiriEngineJobs $job): ?Stored3dModels
    {
        $storedModel = $this->downloadAndStoreModel($job);

        // Auto-attach is already handled in downloadAndStoreModel
        // This method is just for clarity and backward compatibility

        return $storedModel;
    }

    private function getDownloadUrl(KiriEngineJobs $job): ?string
    {
        $apiKey = config('services.kiri.key');

        try {
            // If URL is expired or about to expire, regenerate it
            $isExpired = $job->url_expiry && $job->url_expiry->isPast();
            $isExpiringSoon = $job->url_expiry && $job->url_expiry->diffInMinutes(now()) < 5;

            if (!$job->model_url || $isExpired || $isExpiringSoon) {
                Log::info('Regenerating download URL', ['serialize_id' => $job->serialize_id]);

                $downloadResponse = Http::withToken($apiKey)
                    ->timeout(30)
                    ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$job->serialize_id}");

                if ($downloadResponse->successful()) {
                    $downloadData = $downloadResponse->json();
                    $newUrl = $downloadData['data']['modelUrl'] ?? null;

                    if ($newUrl) {
                        $job->update([
                            'model_url' => $newUrl,
                            'url_expiry' => now()->addHour(),
                        ]);
                        Log::info('New download URL generated', ['url' => $newUrl]);
                        return $newUrl;
                    } else {
                        Log::warning('No modelUrl in API response', ['response' => $downloadData]);
                    }
                } else {
                    Log::warning('API request failed', [
                        'status' => $downloadResponse->status(),
                        'response' => $downloadResponse->body()
                    ]);
                }
                return null;
            }

            Log::info('Using existing download URL', ['url' => $job->model_url]);
            return $job->model_url;
        } catch (Exception $e) {
            Log::error('Error getting download URL', [
                'serialize_id' => $job->serialize_id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function downloadZipFile(string $downloadUrl): ?string
    {
        try {
            Log::info('Downloading zip file', ['url' => $downloadUrl]);

            $response = Http::timeout(120)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'application/zip, application/octet-stream'
                ])
                ->get($downloadUrl);

            if ($response->successful()) {
                $content = $response->body();
                Log::info('Zip file downloaded successfully', ['size' => strlen($content)]);
                return $content;
            } else {
                Log::warning('Failed to download zip file', [
                    'status' => $response->status(),
                    'headers' => $response->headers()
                ]);
                return null;
            }
        } catch (Exception $e) {
            Log::error('Error downloading zip file', [
                'url' => $downloadUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function saveTempZip(string $content, string $serializeId): string
    {
        $tempPath = 'temp/' . $serializeId . '.zip';

        // Ensure temp directory exists
        if (!Storage::disk('local')->exists('temp')) {
            Storage::disk('local')->makeDirectory('temp');
        }

        // Save the file
        Storage::disk('local')->put($tempPath, $content);

        // Get the absolute path
        $absolutePath = Storage::disk('local')->path($tempPath);

        Log::info('Temp zip file saved', [
            'relative_path' => $tempPath,
            'absolute_path' => $absolutePath,
            'file_exists' => file_exists($absolutePath),
            'file_size' => file_exists($absolutePath) ? filesize($absolutePath) : 0
        ]);

        return $absolutePath;
    }

    private function extractZipFile(string $zipPath, string $serializeId): string
    {
        Log::info('Extracting zip file', ['zip_path' => $zipPath, 'serialize_id' => $serializeId]);

        // Use your custom storage path in public/uploads
        $extractPath = 'uploads/3d-models/' . $serializeId;
        $fullExtractPath = public_path($extractPath);

        Log::info('Extraction paths', [
            'extract_path' => $extractPath,
            'full_extract_path' => $fullExtractPath
        ]);

        // Create extraction directory in public/uploads
        if (!file_exists($fullExtractPath)) {
            mkdir($fullExtractPath, 0755, true);
            Log::info('Created extraction directory', ['path' => $fullExtractPath]);
        }

        $zip = new ZipArchive;
        $result = $zip->open($zipPath);

        if ($result === TRUE) {
            Log::info('Zip archive opened successfully', ['file_count' => $zip->numFiles]);

            // Extract files
            $zip->extractTo($fullExtractPath);
            $zip->close();

            // Verify extraction worked by checking if files exist
            $extractedFiles = scandir($fullExtractPath);
            Log::info('Files in extraction directory', [
                'path' => $fullExtractPath,
                'files' => $extractedFiles
            ]);

            Log::info('Zip file extracted successfully', [
                'extract_path' => $extractPath,
                'files_count' => count($extractedFiles) - 2,  // exclude . and ..
                'files' => array_slice($extractedFiles, 2)  // exclude . and ..
            ]);

            return $extractPath;
        } else {
            $errorMessages = [
                ZipArchive::ER_EXISTS => 'File already exists',
                ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                ZipArchive::ER_INVAL => 'Invalid argument',
                ZipArchive::ER_MEMORY => 'Malloc failure',
                ZipArchive::ER_NOENT => 'No such file',
                ZipArchive::ER_NOZIP => 'Not a zip archive',
                ZipArchive::ER_OPEN => "Can't open file",
                ZipArchive::ER_READ => 'Read error',
                ZipArchive::ER_SEEK => 'Seek error',
            ];

            $errorMessage = $errorMessages[$result] ?? "Unknown error ({$result})";
            throw new Exception("Failed to open zip file: {$errorMessage}");
        }
    }

    private function storeModelInDatabase(KiriEngineJobs $job, string $extractedPath): Stored3dModels
    {
        // Get all extracted files from the public directory
        $fullExtractPath = public_path($extractedPath);
        $files = [];

        Log::info('Scanning for extracted files', [
            'extract_path' => $extractedPath,
            'full_extract_path' => $fullExtractPath,
            'directory_exists' => file_exists($fullExtractPath)
        ]);

        if (file_exists($fullExtractPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($fullExtractPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    // Get the correct relative path from public directory
                    $relativePath = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    $relativePath = str_replace('\\', '/', $relativePath);  // Normalize to forward slashes
                    $files[] = $relativePath;

                    Log::info('Found extracted file', [
                        'absolute_path' => $file->getRealPath(),
                        'relative_path' => $relativePath,
                        'file_size' => $file->getSize()
                    ]);
                }
            }
        }

        Log::info('All extracted files found', [
            'count' => count($files),
            'files' => $files
        ]);

        // Find the main model file
        $modelFile = $this->findMainModelFile($files);

        $storedModel = Stored3dModels::create([
            'user_id' => $job->user_id,
            'kiri_engine_job_id' => $job->kiri_engine_job_id,
            'model_name' => '3D Model ' . $job->serialize_id,
            'model_path' => $modelFile ?: $extractedPath,
            'original_filename' => $modelFile ? basename($modelFile) : null,
            'file_size' => $this->calculateFolderSize($fullExtractPath),
            'model_files' => $files,
        ]);

        Log::info('Model stored in database successfully', [
            'stored_model_id' => $storedModel->stored_3d_model_id,
            'model_path' => $storedModel->model_path,
            'file_size' => $storedModel->file_size,
            'files_count' => count($files),
            'main_model_file' => $modelFile
        ]);

        return $storedModel;
    }

    private function findMainModelFile(array $files): ?string
    {
        $modelExtensions = ['.obj', '.gltf', '.glb', '.fbx', '.stl', '.ply'];

        Log::info('Searching for main model file', [
            'available_files' => $files,
            'extensions' => $modelExtensions
        ]);

        foreach ($files as $file) {
            foreach ($modelExtensions as $ext) {
                if (str_contains(strtolower($file), $ext)) {
                    Log::info('Found main model file', [
                        'file' => $file,
                        'extension' => $ext
                    ]);
                    return $file;  // Return the full relative path, not just basename
                }
            }
        }

        Log::warning('No main model file found', ['files' => $files]);
        // If no model file found, return the first file or null
        return count($files) > 0 ? $files[0] : null;
    }

    private function calculateFolderSize(string $path): string
    {
        $size = 0;

        try {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        } catch (Exception $e) {
            Log::warning('Error calculating folder size', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }

        return $this->formatBytes($size);
    }

    private function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function cleanupTempFiles(string $tempZipPath): void
    {
        try {
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
                Log::info('Temp zip file cleaned up', ['path' => $tempZipPath]);
            }
        } catch (Exception $e) {
            Log::warning('Failed to clean up temp file', [
                'path' => $tempZipPath,
                'error' => $e->getMessage()
            ]);
        }
    }
}
