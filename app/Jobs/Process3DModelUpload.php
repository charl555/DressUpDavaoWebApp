<?php

namespace App\Jobs;

use App\Models\KiriEngineJobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Process3DModelUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $jobId;
    protected $images;
    protected $apiKey;

    /**
     * Create a new job instance.
     */
    public function __construct(int $jobId, array $images, string $apiKey)
    {
        $this->jobId = $jobId;
        $this->images = $images;
        $this->apiKey = $apiKey;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $job = KiriEngineJobs::find($this->jobId);
        
        if (!$job) {
            Log::error('3D Model Upload Job: Job not found', ['job_id' => $this->jobId]);
            return;
        }

        try {
            $job->update(['status' => 'uploading']);

            // Prepare images for upload
            $multipart = [];
            $uploadedFiles = [];

            foreach ($this->images as $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    $fullPath = Storage::disk('public')->path($imagePath);
                    $filename = basename($imagePath);
                    
                    $multipart[] = [
                        'name' => 'images[]',
                        'contents' => fopen($fullPath, 'r'),
                        'filename' => $filename,
                    ];
                    
                    $uploadedFiles[] = $imagePath;
                } else {
                    Log::warning('3D Model Upload: Image file not found', ['path' => $imagePath]);
                }
            }

            if (empty($multipart)) {
                throw new \Exception('No valid images found for upload');
            }

            // Add required API parameters
            $multipart[] = ['name' => 'modelQuality', 'contents' => '0'];
            $multipart[] = ['name' => 'textureQuality', 'contents' => '0'];
            $multipart[] = ['name' => 'fileFormat', 'contents' => 'GLB'];
            $multipart[] = ['name' => 'isMask', 'contents' => '0'];
            $multipart[] = ['name' => 'textureSmoothing', 'contents' => '1'];
            $multipart[] = ['name' => 'notifyUrl', 'contents' => route('kiri.webhook')];

            Log::info('3D Model Upload Job: Sending request to Kiri Engine API', [
                'job_id' => $job->kiri_engine_job_id,
                'image_count' => count($uploadedFiles),
                'api_endpoint' => 'https://api.kiriengine.app/api/v1/open/photo/image'
            ]);

            $response = Http::withToken($this->apiKey)
                ->timeout(300)  // 5 minutes for upload
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->send('POST', 'https://api.kiriengine.app/api/v1/open/photo/image', [
                    'multipart' => $multipart,
                ]);

            Log::info('3D Model Upload Job: Kiri Engine API response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $responseBody = $response->json();

                if (isset($responseBody['data']['serialize'])) {
                    $kiriSerializeId = $responseBody['data']['serialize'];

                    $job->update([
                        'serialize_id' => $kiriSerializeId,
                        'status' => 'processing',
                        'notes' => 'Successfully submitted to Kiri Engine via background job',
                    ]);

                    Log::info('3D Model Upload Job: Kiri Engine job created successfully', [
                        'job_id' => $job->kiri_engine_job_id,
                        'serialize_id' => $kiriSerializeId
                    ]);
                } else {
                    $errorMsg = $responseBody['msg'] ?? 'No serialize ID in response';
                    $job->update([
                        'status' => 'failed',
                        'error_message' => "API response error: {$errorMsg}",
                        'notes' => 'Failed during background job processing'
                    ]);
                    
                    Log::error('3D Model Upload Job: No serialize ID in response', [
                        'job_id' => $job->kiri_engine_job_id,
                        'response' => $responseBody
                    ]);
                }
            } else {
                $errorMsg = $response->body();
                $job->update([
                    'status' => 'failed',
                    'error_message' => "API request failed: HTTP {$response->status()}",
                    'notes' => "Background job failed: {$errorMsg}"
                ]);
                
                Log::error('3D Model Upload Job: API request failed', [
                    'job_id' => $job->kiri_engine_job_id,
                    'status' => $response->status(),
                    'response' => $errorMsg
                ]);
            }

            // Clean up uploaded files after processing
            foreach ($uploadedFiles as $filePath) {
                try {
                    Storage::disk('public')->delete($filePath);
                } catch (\Exception $e) {
                    Log::warning('3D Model Upload Job: Failed to delete uploaded file', [
                        'file' => $filePath,
                        'error' => $e->getMessage()
                    ]);
                }
            }

        } catch (\Exception $e) {
            $job->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'notes' => 'Background job processing failed'
            ]);

            Log::error('3D Model Upload Job: Processing failed', [
                'job_id' => $job->kiri_engine_job_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw to trigger job retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $job = KiriEngineJobs::find($this->jobId);
        
        if ($job) {
            $job->update([
                'status' => 'failed',
                'error_message' => "Job failed after {$this->tries} attempts: {$exception->getMessage()}",
                'notes' => 'Background job failed permanently'
            ]);
        }

        Log::error('3D Model Upload Job: Job failed permanently', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
