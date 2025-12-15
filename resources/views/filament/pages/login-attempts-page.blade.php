<x-filament-panels::page>
    <div class="space-y-4">
        {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">
                        {{ \App\Models\LoginAttempt::count() }}
                    </div>
                    <div class="text-sm text-gray-500">Total Attempts</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        {{ \App\Models\LoginAttempt::where('success', true)->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Successful</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">
                        {{ \App\Models\LoginAttempt::where('success', false)->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Failed</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-warning-600">
                        {{ \App\Models\LoginAttempt::whereDate('attempted_at', today())->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Today</div>
                </div>
            </x-filament::section>
        </div> --}}

        {{ $this->table }}
    </div>
</x-filament-panels::page>