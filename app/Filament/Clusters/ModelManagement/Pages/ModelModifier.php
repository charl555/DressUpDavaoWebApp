<?php

namespace App\Filament\Clusters\ModelManagement\Pages;

use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\Product3dModels;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class ModelModifier extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected static ?string $slug = 'model-modifier';
    protected string $view = 'filament.clusters.model-management.pages.model-modifier';
    protected static ?string $cluster = ModelManagementCluster::class;
    protected static ?string $navigationLabel = 'Model Modifier';
    protected static ?string $title = 'Model Modifier';
    protected static ?int $navigationSort = 4;
    protected static bool $shouldRegisterNavigation = false;

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public $models;
    public $selectedModel = null;

    public function mount()
    {
        $this->models = Product3dModels::with('product')->get();
    }

    public function selectModel($modelPath)
    {
        $this->selectedModel = asset('uploads/' . $modelPath);
    }
}
