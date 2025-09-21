<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductsResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;
    protected $occasionsData = [];
    protected $measurementsData = [];
    protected $thumbnail = null;
    protected $galleryImages = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $this->occasionsData = explode(',', $data['occasions'] ?? []);
        unset($data['occasions']);

        $this->measurementsData = Arr::only($data, [
            'gown_length',
            'gown_upper_chest',
            'gown_chest',
            'gown_waist',
            'gown_hips',
            'jacket_chest',
            'jacket_length',
            'jacket_shoulder',
            'jacket_sleeve_length',
            'jacket_sleeve_width',
            'jacket_bicep',
            'jacket_arm_hole',
            'jacket_waist',
            'trouser_waist',
            'trouser_hip',
            'trouser_inseam',
            'trouser_outseam',
            'trouser_thigh',
            'trouser_leg_opening',
            'trouser_crotch',
        ]);
        $data = Arr::except($data, array_keys($this->measurementsData));

        // Handle images (updated column names)
        $this->thumbnail = $data['thumbnail_image'] ?? null;
        $this->galleryImages = $data['images'] ?? [];

        unset($data['thumbnail_image'], $data['images']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $product = $this->record;

        foreach ($this->occasionsData as $occasion_name) {
            $product->occasions()->create([
                'occasion_name' => $occasion_name,
            ]);
        }

        $product->product_measurements()->create(
            array_merge($this->measurementsData, ['product_id' => $product->product_id])
        );

        $product->product_images()->create([
            'thumbnail_image' => $this->thumbnail ?? null,
            'images' => !empty($this->galleryImages) ? $this->galleryImages : [],
        ]);

        Notification::make()
            ->title('New Product Created')
            ->body("A new product named '{$product->name}' was created.")
            ->success()
            ->sendToDatabase(Auth::user());
    }
}
