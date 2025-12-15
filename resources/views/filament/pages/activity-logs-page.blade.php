<x-filament-panels::page>
    <div class="space-y-4">
        {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">
                        {{ \App\Models\ActivityLog::count() }}
                    </div>
                    <div class="text-sm text-gray-500">Total Activities</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        {{ \App\Models\ActivityLog::whereIn('action', ['login', 'create', 'approve'])->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Positive Actions</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">
                        {{ \App\Models\ActivityLog::whereIn('action', ['delete', 'reject', 'block'])->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Negative Actions</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">
                        {{ \App\Models\ActivityLog::whereDate('created_at', today())->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Today's Activities</div>
                </div>
            </x-filament::section>
        </div> --}}

        {{ $this->table }}
    </div>
</x-filament-panels::page>