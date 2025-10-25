<x-filament-panels::page>
    @php
        $shop = \App\Models\Shops::where('user_id', auth()->id())->first();
        $hasAccess = $shop?->allow_3d_model_access ?? false;
    @endphp

    @if(!$hasAccess)
        <div class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Access Denied
                </x-slot>

                <x-slot name="description">
                    Your account currently does not have access to this page.
                </x-slot>
            </x-filament::section>
        </div>
    @else
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <form wire:submit.prevent="submit">
                    {{ $this->form }}

                    <x-filament::button type="submit" wire:loading.attr="disabled" class="w-full" size="lg">
                        <span wire:loading.remove>Generate 3D Model</span>
                        <span wire:loading>Uploading...</span>
                    </x-filament::button>

                    @if ($isProcessing)
                        <div class="mt-4 p-3 bg-blue-50 text-blue-700 rounded">
                            <div class="flex items-center">
                                <x-filament::loading-indicator class="w-5 h-5 mr-2" />
                                Uploading images to Kiri Engine...
                            </div>
                        </div>
                    @endif

                    @if ($statusMessage)
                        <div class="mt-4 p-3 bg-gray-50 text-gray-700 rounded">
                            {{ $statusMessage }}
                            @if ($serialize)
                                <p class="text-sm mt-2">
                                    Job ID: <span class="font-mono">{{ $serialize }}</span>
                                </p>
                                <p class="text-sm mt-1">
                                    Check the <a href="{{ url('/admin/download-3d-models') }}"
                                        class="text-primary-600 underline">Download 3D Models</a> page for progress.
                                </p>
                            @endif
                        </div>
                    @endif

                    @error('general')
                        <div class="mt-4 p-3 bg-red-50 text-red-700 rounded">
                            {{ $message }}
                        </div>
                    @enderror

                    @error('images')
                        <div class="mt-4 p-3 bg-red-50 text-red-700 rounded">
                            {{ $message }}
                        </div>
                    @enderror
                </form>
            </div>
        </div>
    @endif
</x-filament-panels::page>