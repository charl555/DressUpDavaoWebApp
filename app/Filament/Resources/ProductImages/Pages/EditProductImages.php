<?php

namespace App\Filament\Resources\ProductImages\Pages;

use App\Filament\Resources\ProductImages\ProductImagesResource;
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
        // Save thumbnail (single file)
        if (!empty($data['thumbnail_image'])) {
            $record->thumbnail_image = $data['thumbnail_image'];
        }

        // Save images (multiple, stored as JSON)
        if (!empty($data['images']) && is_array($data['images'])) {
            $record->images = json_encode($data['images']);
        } else {
            $record->images = json_encode([]);
        }

        $record->save();

        return $record;
    }
}
