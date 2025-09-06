<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\Product3dModels;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class ModelModifier extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected string $view = 'filament.clusters.model-management.pages.model-modifier';
    protected static ?string $cluster = ModelManagementCluster::class;
    protected static ?string $navigationLabel = 'Model Modifier';
    protected static ?string $title = 'Model Modifier';
    public $models;
    public $selectedModel = null;

    public function mount()
    {
        $this->models = Product3dModels::with('product')->get();
    }

    public function selectModel($modelPath)
    {
        $this->selectedModel = asset('storage/' . $modelPath);
    }
}
