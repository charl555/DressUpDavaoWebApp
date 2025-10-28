<?php

namespace App\Http\Controllers;

use App\Models\KiriEngineJobs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class KiriWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('KIRI Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        // Verify the webhook signature if you set a signing secret
        $signingSecret = config('services.kiri.webhook_secret');
        if ($signingSecret) {
            $signature = $request->header('X-Kiri-Signature');  // Adjust header name based on KIRI's docs

            /*
             * Temporarily disable for testing
             * if (!$this->verifySignature($request->getContent(), $signature, $signingSecret)) {
             *     Log::warning('Invalid webhook signature', [
             *         'received_signature' => $signature,
             *         'expected_secret' => $signingSecret
             *     ]);
             *     return response()->json(['error' => 'Invalid signature'], 401);
             * }
             */
            Log::info('Signature verification temporarily disabled for testing');
        }
        // Validate required fields
        $data = $request->validate([
            'serialize' => 'required|string',
            'status' => 'required|string',
            'modelUrl' => 'nullable|string',
            'error' => 'nullable|string',
            'message' => 'nullable|string',  // Some APIs send message instead of error
        ]);

        try {
            // Find the job by serialize_id
            $job = KiriEngineJobs::where('serialize_id', $data['serialize'])->first();

            if (!$job) {
                Log::warning('KIRI Webhook: Job not found', ['serialize_id' => $data['serialize']]);
                return response()->json(['error' => 'Job not found'], 404);
            }

            // Update job status and model URL
            $updateData = [
                'status' => $data['status'],
                'updated_at' => now(),
            ];

            if ($data['status'] === 'finished' && isset($data['modelUrl'])) {
                $updateData['model_url'] = $data['modelUrl'];
                $updateData['url_expiry'] = now()->addDays(3);  // URLs typically expire after 3 days
            } elseif ($data['status'] === 'failed') {
                // Handle error message from either 'error' or 'message' field
                $errorMessage = $data['error'] ?? $data['message'] ?? 'Unknown error occurred';
                $updateData['error_message'] = $errorMessage;
            }

            $job->update($updateData);

            Log::info('KIRI Webhook: Job updated successfully', [
                'serialize_id' => $data['serialize'],
                'status' => $data['status'],
                'model_url_updated' => isset($data['modelUrl'])
            ]);

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('KIRI Webhook processing failed', [
                'serialize_id' => $data['serialize'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Verify webhook signature
     */
    private function verifySignature(string $payload, ?string $signature, string $secret): bool
    {
        if (!$signature) {
            return false;
        }

        // KIRI might use HMAC-SHA256 - adjust based on their documentation
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
