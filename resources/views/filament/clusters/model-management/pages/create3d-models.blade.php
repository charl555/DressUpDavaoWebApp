<x-filament-panels::page>
    <div class="space-y-6 p-4">

        <div class="bg-white p-6 rounded-lg shadow-md">
            <form wire:submit.prevent="submit">
                {{ $this->form }}

                <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="submit, images">
                    <span wire:loading.remove wire:target="submit, images">Upload and Generate 3D Model</span>
                    <span wire:loading wire:target="submit, images">Uploading & Generating...</span>
                </x-filament::button>


                @if ($statusMessage)
                    <div class="mt-4 p-3 rounded-md bg-gray-100 text-gray-700">
                        {{ $statusMessage }}
                        @if ($serialize)
                            <p class="text-sm italic mt-1">
                                Your job ID is: <span class="font-bold">{{ $serialize }}</span>.
                                You can track its full progress in the <a href="/jobs-3d-model" a
                                    class="underline text-blue-600">3D Model
                                    Jobs</a> section.
                            </p>
                        @endif
                    </div>
                @endif

                @error('general')
                    <div class="mt-4 p-3 rounded-md bg-red-100 text-red-700">
                        {{ $message }}
                    </div>
                @enderror
            </form>
        </div>


        @if ($modelUrl)
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h2 class="text-2xl font-semibold mb-4">Immediate Download Link (Will disappear on refresh)</h2>
                <p class="text-green-700 mb-4">Your 3D model link (serial: {{ $serialize }}) is: <a href="{{ $modelUrl }}"
                        target="_blank" class="underline text-blue-600">Download Model</a></p>
                <p class="text-sm text-gray-500 mt-2">
                    * This link is for immediate access. For persistent access and job history, please check the 3D Model
                    Jobs section.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>