<?php

namespace App\Filament\Pages;

use App\Models\Stored3dModels;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;
use BackedEnum;

class View3DModel extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected string $view = 'filament.pages.view3-d-model';
    protected static ?string $title = 'Viewing 3D Model';
    protected static bool $shouldRegisterNavigation = false;

    public $modelUrl;
    public $clippingData;
    public $modelName;
    public $isStoredModel = true;  // Always true now since we're only handling stored models
    public $debugInfo = [];

    public function mount($id)
    {
        // Load stored 3D model from stored3d_models table
        $this->loadStoredModel($id);
    }

    private function loadStoredModel($id)
    {
        Log::info('Loading stored model', ['stored_model_id' => $id]);

        $storedModel = Stored3dModels::with('kiriEngineJob')->find($id);

        if (!$storedModel) {
            Log::error('Stored model not found', ['id' => $id]);
            abort(404, 'Stored 3D model not found.');
        }

        Log::info('Stored model found', [
            'stored_model_id' => $storedModel->stored_3d_model_id,
            'model_name' => $storedModel->model_name,
            'model_path' => $storedModel->model_path,
            'has_files' => !empty($storedModel->model_files)
        ]);

        // Use the model_path directly from the database
        $modelFile = $storedModel->model_path;

        if (!$modelFile) {
            $errorMsg = 'No model path found in stored model. Available files: ' . json_encode($storedModel->model_files ?? []);
            Log::error($errorMsg);
            abort(404, $errorMsg);
        }

        // Verify the file actually exists
        $fullPath = public_path($modelFile);
        if (!file_exists($fullPath)) {
            $errorMsg = "3D model file not found at: {$fullPath}";
            Log::error($errorMsg);
            abort(404, $errorMsg);
        }

        $this->modelUrl = asset($modelFile);
        $this->clippingData = [];  // No clipping data for stored models
        $this->modelName = $storedModel->model_name;
        $this->isStoredModel = true;

        // Debug information
        $this->debugInfo = [
            'stored_model_id' => $storedModel->stored_3d_model_id,
            'model_file' => $modelFile,
            'model_url' => $this->modelUrl,
            'full_path' => $fullPath,
            'file_exists' => file_exists($fullPath),
            'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0,
            'all_files' => $storedModel->model_files ?? []
        ];

        Log::info('Loading stored 3D model', $this->debugInfo);
    }
}
