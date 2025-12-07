<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProducts extends EditRecord
{
    protected static string $resource = ProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $product = $this->record;

        // Get the events from the form data - it's already an array from the select field
        $events = $this->data['product_events'] ?? [];

        // Remove existing events (delete all and replace)
        $product->events()->delete();

        // Reinsert new events
        if (is_array($events) && !empty($events)) {
            foreach ($events as $eventName) {
                if (!empty(trim($eventName))) {
                    $product->events()->create([
                        'event_name' => trim($eventName),
                    ]);
                }
            }
        }
    }

    // This method ensures the events field is populated with existing data when editing
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $product = $this->record;

        // Load existing events as an array of event names
        // This matches what the select field expects
        if ($product->exists) {
            $existingEvents = $product->events->pluck('event_name')->toArray();
            $data['product_events'] = $existingEvents;
        } else {
            $data['product_events'] = [];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
