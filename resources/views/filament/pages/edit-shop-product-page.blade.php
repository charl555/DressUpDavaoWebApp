<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="save" class="space-y-6">
            {{ $this->form }}

            <!-- Form Actions -->
            <div class="fi-form-actions">
                <div class="fi-ac gap-3 flex flex-wrap items-center justify-end">
                    <x-filament::button type="submit">

                        Save Changes
                    </x-filament::button>

                    <x-filament::button color="gray" wire:click="cancel">

                        Cancel
                    </x-filament::button>

                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>