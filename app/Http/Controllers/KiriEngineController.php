<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;  // For handling file uploads and downloads

class KiriEngineController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.kiri_engine.api_key');
        $this->baseUrl = config('services.kiri_engine.base_url');

        if (empty($this->apiKey)) {
            // Handle case where API key is not set (e.g., throw exception, log error)
            throw new \Exception('KIRI Engine API key is not configured.');
        }
    }

    /**
     * Upload images to KIRI Engine for 3D model generation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImages(Request $request)
    {
        $request->validate([
            'imagesFiles' => 'required|array|min:20|max:300',  // KIRI Engine requires 20-300 images
            'imagesFiles.*' => 'image|mimes:jpeg,png,jpg|max:5000',  // Max 5MB per image, adjust as needed
            'modelQuality' => 'required|numeric|min:0|max:1',  // 0 for low, 1 for high
            'textureQuality' => 'required|numeric|min:0|max:1',  // 0 for low, 1 for high
            'fileFormat' => 'required|string|in:OBJ,GLTF',  // GLTF is often preferred for web
            'isMask' => 'nullable|boolean',  // 0 or 1
            'textureSmoothing' => 'nullable|boolean',  // 0 or 1
        ]);

        try {
            // Build the multipart form data
            $formParams = [
                'modelQuality' => $request->input('modelQuality', 1),  // Default to high
                'textureQuality' => $request->input('textureQuality', 1),  // Default to high
                'fileFormat' => $request->input('fileFormat', 'GLTF'),  // Default to GLTF
                'isMask' => $request->boolean('isMask', false) ? '1' : '0',  // Default to 0 (false)
                'textureSmoothing' => $request->boolean('textureSmoothing', true) ? '1' : '0',  // Default to 1 (true)
            ];

            $http = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]);

            // Add each image file to the request
            foreach ($request->file('imagesFiles') as $image) {
                $http->attach(
                    'imagesFiles[]',  // KIRI Engine expects 'imagesFiles' as an array of files
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName(),
                    ['Content-Type' => $image->getMimeType()]
                );
            }

            // Make the POST request to KIRI Engine API
            $response = $http->post($this->baseUrl . 'photo/image', $formParams);

            // Check for API errors
            if ($response->failed()) {
                \Log::error('KIRI Engine Image Upload Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload images to KIRI Engine. ' . $response->json('msg', 'Unknown error.'),
                    'api_response' => $response->json()
                ], $response->status());
            }

            $responseData = $response->json();

            // Store the serialize ID in your database if needed, linked to a product/user
            // Example: Product::find($productId)->update(['kiri_serialize_id' => $responseData['data']['serialize']]);

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully.',
                'serialize' => $responseData['data']['serialize'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in KiriEngineController@uploadImages: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An internal server error occurred.'], 500);
        }
    }

    /**
     * Get the download link for a 3D model from KIRI Engine.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModelDownloadLink(Request $request)
    {
        $request->validate([
            'serialize' => 'required|string|size:32',  // KIRI Engine serialize ID is 32 chars
        ]);

        try {
            $serialize = $request->input('serialize');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . 'model/getModelZip', [
                'serialize' => $serialize,
            ]);

            if ($response->failed()) {
                \Log::error('KIRI Engine Model Download Link Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get model download link from KIRI Engine. ' . $response->json('msg', 'Unknown error.'),
                    'api_response' => $response->json()
                ], $response->status());
            }

            $responseData = $response->json();

            return response()->json([
                'success' => true,
                'modelUrl' => $responseData['data']['modelUrl'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in KiriEngineController@getModelDownloadLink: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An internal server error occurred.'], 500);
        }
    }
}
