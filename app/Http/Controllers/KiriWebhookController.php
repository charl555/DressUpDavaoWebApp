<?php

namespace App\Http\Controllers;

use App\Models\KiriEngineJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KiriWebhookController extends Controller
{
    public function modelReady(Request $request)
    {
        // Kiri Engine typically sends 'serialize' and 'status'
        $serialize = $request->input('serialize');
        $status = $request->input('status');
        // We added 'job_id' to the notifyUrl in the Livewire component for direct lookup
        $jobId = $request->input('job_id');

        // Prioritize finding by job_id if available, otherwise fallback to serialize
        $job = null;
        if ($jobId) {
            $job = KiriEngineJobs::find($jobId);
        } elseif ($serialize) {
            $job = KiriEngineJobs::where('serialize_id', $serialize)->first();
        }

        if (!$job) {
            Log::warning("Webhook received for unknown Kiri Engine serialize ID: $serialize or job ID: $jobId");
            return response()->json(['message' => 'Unknown serial ID or job ID.'], 404);
        }

        if ($status === 'finished') {
            $apiKey = config('services.kiri.key');
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(120)
                    ->get("https://api.kiriengine.app/api/v1/open/model/getModelZip?serialize={$serialize}");

                if ($response->successful() && isset($response['data']['modelUrl'])) {
                    $modelUrl = $response['data']['modelUrl'];
                    $job->update([
                        'status' => 'finished',
                        'model_url' => $modelUrl,
                    ]);
                    Cache::put("kiri_model_{$serialize}", $modelUrl, now()->addHours(2));
                    Log::info("Model ready and job updated in DB: $modelUrl (Serial ID: $serialize)");
                } else {
                    $errorMsg = $response->json('message', 'Failed to retrieve model URL from Kiri Engine.');
                    $job->update([
                        'status' => 'failed',
                        'notes' => 'Model retrieval failed from Kiri Engine: ' . $errorMsg,
                    ]);
                    Log::warning("Model retrieval failed for serialize: $serialize. Error: " . $errorMsg . ' Response: ' . $response->body());
                }
            } catch (\Exception $e) {
                $job->update([
                    'status' => 'failed',
                    'notes' => 'API call to getModelZip failed: ' . $e->getMessage(),
                ]);
                Log::error("Exception retrieving model for serialize: $serialize. Error: " . $e->getMessage());
            }
        } else {
            // For other statuses (e.g., 'processing', 'failed')
            $job->update([
                'status' => $status,
                'notes' => 'Status update from Kiri: ' . $status,
            ]);
            Log::info("Kiri Engine webhook status update: $status for serialize ID: $serialize (Job ID: {$job->kiri_engine_job_id})");
        }

        return response()->json(['message' => 'Webhook received and processed.']);
    }
}
