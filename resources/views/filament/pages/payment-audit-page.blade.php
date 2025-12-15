<x-filament-panels::page>
    <div class="space-y-4">
        {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">
                        {{ \App\Models\Payments::count() }}
                    </div>
                    <div class="text-sm text-gray-500">Total Payments</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">
                        ₱{{ number_format(\App\Models\Payments::sum('amount_paid'), 2) }}
                    </div>
                    <div class="text-sm text-gray-500">Total Amount</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">
                        {{ \App\Models\Payments::whereDate('payment_date', today())->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Today's Payments</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-center">
                    <div class="text-2xl font-bold text-warning-600">
                        ₱{{ number_format(\App\Models\Payments::whereDate('payment_date', today())->sum('amount_paid'),
                        2) }}
                    </div>
                    <div class="text-sm text-gray-500">Today's Amount</div>
                </div>
            </x-filament::section>
        </div> --}}

        {{ $this->table }}
    </div>
</x-filament-panels::page>