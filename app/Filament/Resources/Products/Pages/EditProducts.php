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

        // Get the events from the form data
        $eventsString = $this->data['product_events'] ?? '';

        // Process events data (same logic as CreateProducts)
        $eventsData = $this->processEventsData($eventsString);

        // Remove existing events
        $product->events()->delete();

        // Reinsert new events
        foreach ($eventsData as $eventName) {
            if (!empty($eventName)) {
                $product->events()->create([
                    'event_name' => $eventName,
                ]);
            }
        }
    }

    /**
     * Process events data from comma-separated string to array
     */
    protected function processEventsData(?string $eventsString): array
    {
        if (empty($eventsString)) {
            return [];
        }

        $events = array_map(function ($event) {
            // Remove quotes, trim whitespace, and ensure it's not empty
            $cleanEvent = trim($event, " \t\n\r\0\v\"'");
            return $cleanEvent;
        }, explode(',', $eventsString));

        // Remove empty values and duplicates
        return array_values(array_unique(array_filter($events)));
    }

    // This method ensures the events field is populated with existing data when editing
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $product = $this->record;

        // Load existing events into comma-separated string format
        $existingEvents = $product->events->pluck('event_name')->toArray();
        $data['product_events'] = implode(', ', $existingEvents);

        return $data;
    }
}
