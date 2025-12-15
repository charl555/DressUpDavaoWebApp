<x-filament-panels::page>
    <div class="space-y-4">
        {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">
                        {{ \App\Models\LoginBlock::count() }}
                    </div>
                    <div class="text-sm text-gray-500">Total Blocks</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">
                        {{ \App\Models\LoginBlock::where('blocked_until', '>', now())->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Active Blocks</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        {{ \App\Models\LoginBlock::where('blocked_until', '<=', now())->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Expired Blocks</div>
                </div>
            </x-filament::section>
        </div> --}}

        {{ $this->table }}
    </div>
</x-filament-panels::page>