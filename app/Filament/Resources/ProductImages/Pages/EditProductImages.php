<?php

namespace App\Filament\Resources\ProductImages\Pages;

use App\Filament\Resources\ProductImages\ProductImagesResource;
use App\Models\ProductImages;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductImages extends EditRecord
{
    protected static string $resource = ProductImagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $productId = $data['product_id'];

        if (!empty($data['thumbnail'])) {
            ProductImages::updateOrCreate(
                [
                    'product_id' => $productId,
                    'type' => 'thumbnail',
                ],
                [
                    'image_path' => $data['thumbnail'],
                ]
            );
        }

        ProductImages::where('product_id', $productId)
            ->where('type', 'gallery')
            ->delete();

        if (!empty($data['image_path']) && is_array($data['image_path'])) {
            foreach ($data['image_path'] as $image) {
                ProductImages::create([
                    'product_id' => $productId,
                    'image_path' => $image,
                    'type' => 'gallery',
                ]);
            }
        } else {
            ProductImages::create([
                'product_id' => $productId,
                'image_path' => null,
                'type' => 'gallery',
            ]);
        }

        return $record;
    }
}
