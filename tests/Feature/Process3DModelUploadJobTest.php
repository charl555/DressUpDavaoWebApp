<?php

namespace Tests\Feature;

use App\Jobs\Process3DModelUpload;
use App\Models\KiriEngineJobs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Process3DModelUploadJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        
        // Set up fake storage
        Storage::fake('public');
    }

    public function test_job_can_be_dispatched()
    {
        Queue::fake();

        $job = KiriEngineJobs::create([
            'user_id' => $this->user->id,
            'serialize_id' => 'temp_test_123',
            'status' => 'uploading',
            'kiri_options' => [],
            'is_downloaded' => false,
            'url_expiry' => now()->addDays(7),
        ]);

        $imagePaths = ['test-image1.jpg', 'test-image2.jpg'];
        $apiKey = 'test-api-key';

        Process3DModelUpload::dispatch($job->kiri_engine_job_id, $imagePaths, $apiKey);

        Queue::assertPushed(Process3DModelUpload::class, function ($job) use ($imagePaths, $apiKey) {
            return $job->jobId === 1 && 
                   $job->images === $imagePaths && 
                   $job->apiKey === $apiKey;
        });
    }

    public function test_job_handles_missing_job_record()
    {
        $imagePaths = ['test-image1.jpg'];
        $apiKey = 'test-api-key';
        
        $job = new Process3DModelUpload(999, $imagePaths, $apiKey); // Non-existent job ID
        
        // This should not throw an exception, just log and return
        $job->handle();
        
        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    public function test_job_validates_image_files_exist()
    {
        $job = KiriEngineJobs::create([
            'user_id' => $this->user->id,
            'serialize_id' => 'temp_test_456',
            'status' => 'uploading',
            'kiri_options' => [],
            'is_downloaded' => false,
            'url_expiry' => now()->addDays(7),
        ]);

        // Create fake images
        Storage::disk('public')->put('test-image1.jpg', 'fake image content');
        Storage::disk('public')->put('test-image2.jpg', 'fake image content');

        $imagePaths = ['test-image1.jpg', 'test-image2.jpg'];
        $apiKey = 'test-api-key';

        // Mock successful API response
        Http::fake([
            'api.kiriengine.app/*' => Http::response([
                'data' => [
                    'serialize' => 'kiri_serialize_123'
                ]
            ], 200)
        ]);

        $jobInstance = new Process3DModelUpload($job->kiri_engine_job_id, $imagePaths, $apiKey);
        $jobInstance->handle();

        // Verify job was updated
        $job->refresh();
        $this->assertEquals('processing', $job->status);
        $this->assertEquals('kiri_serialize_123', $job->serialize_id);
    }

    public function test_job_handles_api_failure()
    {
        $job = KiriEngineJobs::create([
            'user_id' => $this->user->id,
            'serialize_id' => 'temp_test_789',
            'status' => 'uploading',
            'kiri_options' => [],
            'is_downloaded' => false,
            'url_expiry' => now()->addDays(7),
        ]);

        // Create fake images
        Storage::disk('public')->put('test-image1.jpg', 'fake image content');

        $imagePaths = ['test-image1.jpg'];
        $apiKey = 'test-api-key';

        // Mock API failure
        Http::fake([
            'api.kiriengine.app/*' => Http::response(['error' => 'API Error'], 500)
        ]);

        $jobInstance = new Process3DModelUpload($job->kiri_engine_job_id, $imagePaths, $apiKey);
        
        try {
            $jobInstance->handle();
        } catch (\Exception $e) {
            // Expected to throw exception for retry
        }

        // Verify job was marked as failed
        $job->refresh();
        $this->assertEquals('failed', $job->status);
        $this->assertStringContains('API request failed', $job->error_message);
    }
}
