<?php

namespace App\Filament\Pages;

use App\Models\Products;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class View3DModel extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected string $view = 'filament.pages.view3-d-model';
    protected static ?string $title = 'Viewing 3D Model';
    protected static bool $shouldRegisterNavigation = false;

    public $modelUrl;
    public $clippingData;
    public $product;

    public function mount($id)
    {
        $product = Products::with('product_3d_models')->findOrFail($id);

        if (!$product->product_3d_models) {
            abort(404, '3D model not found.');
        }

        $this->product = $product;
        $this->modelUrl = asset('storage/' . $product->product_3d_models->model_path);
        $this->clippingData = $product->product_3d_models->clipping_planes_data ?? [];
    }
}
