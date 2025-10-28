<?php

namespace Tests\Feature;

use App\Models\KiriEngineJobs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class KiriWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create();
    }

    public function test_webhook_handles_successful_completion()
    {
        // Create a test job
        $job = KiriEngineJobs::create([
            'user_id' => $this->user->id,
            'serialize_id' => 'test_serialize_123',
            'status' => 'processing',
            'kiri_options' => [],
            'is_downloaded' => false,
            'url_expiry' => now()->addDays(7),
        ]);

        // Simulate webhook payload for successful completion
        $payload = [
            'serialize' => 'test_serialize_123',
            'status' => 'finished',
            'modelUrl' => 'https://example.com/model.glb',
        ];

        $response = $this->postJson('/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify job was updated
        $job->refresh();
        $this->assertEquals('finished', $job->status);
        $this->assertEquals('https://example.com/model.glb', $job->model_url);
        $this->assertNotNull($job->url_expiry);
    }

    public function test_webhook_handles_failure()
    {
        // Create a test job
        $job = KiriEngineJobs::create([
            'user_id' => $this->user->id,
            'serialize_id' => 'test_serialize_456',
            'status' => 'processing',
            'kiri_options' => [],
            'is_downloaded' => false,
            'url_expiry' => now()->addDays(7),
        ]);

        // Simulate webhook payload for failure
        $payload = [
            'serialize' => 'test_serialize_456',
            'status' => 'failed',
            'error' => 'Processing failed due to insufficient images',
        ];

        $response = $this->postJson('/webhook', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify job was updated
        $job->refresh();
        $this->assertEquals('failed', $job->status);
        $this->assertEquals('Processing failed due to insufficient images', $job->error_message);
    }

    public function test_webhook_handles_nonexistent_job()
    {
        $payload = [
            'serialize' => 'nonexistent_job',
            'status' => 'finished',
            'modelUrl' => 'https://example.com/model.glb',
        ];

        $response = $this->postJson('/webhook', $payload);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Job not found']);
    }

    public function test_webhook_validates_required_fields()
    {
        $payload = [
            'status' => 'finished',
            // Missing 'serialize' field
        ];

        $response = $this->postJson('/webhook', $payload);

        $response->assertStatus(422);  // Validation error
    }

    public function test_webhook_logs_requests()
    {
        Log::shouldReceive('info')
            ->twice()  // Once for webhook received, once for job updated
            ->with(\Mockery::type('string'), \Mockery::type('array'));

        $job = KiriEngineJobs::create([
            'user_id' => $this->user->id,
            'serialize_id' => 'test_serialize_789',
            'status' => 'processing',
            'kiri_options' => [],
            'is_downloaded' => false,
            'url_expiry' => now()->addDays(7),
        ]);

        $payload = [
            'serialize' => 'test_serialize_789',
            'status' => 'finished',
            'modelUrl' => 'https://example.com/model.glb',
        ];

        $response = $this->postJson('/webhook', $payload);
        $response->assertStatus(200);
    }
}
