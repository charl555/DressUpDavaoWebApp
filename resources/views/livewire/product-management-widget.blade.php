<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card 1 --}}
            <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-md hover:shadow-lg cursor-pointer transition"
                onclick="window.location='{{ route('filament.admin.resources.products.index') }}'">
                <x-heroicon-o-archive class="w-10 h-10 text-primary-600 mb-2" />
                <h3 class="text-lg font-semibold">Products</h3>
                <p class="text-sm text-gray-500">Manage products</p>
            </div>

            {{-- Card 2 --}}
            <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-md hover:shadow-lg cursor-pointer transition"
                onclick="window.location='{{ route('filament.admin.resources.product-measurements.index') }}'">
                <x-heroicon-o-shopping-cart class="w-10 h-10 text-primary-600 mb-2" />
                <h3 class="text-lg font-semibold">Product Measurements</h3>
                <p class="text-sm text-gray-500">Manage product measurements</p>
            </div>

            {{-- Card 3 --}}
            <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-md hover:shadow-lg cursor-pointer transition"
                onclick="window.location='{{ route('filament.admin.resources.product-images.index') }}'">
                <x-heroicon-o-users class="w-10 h-10 text-primary-600 mb-2" />
                <h3 class="text-lg font-semibold">Product Images</h3>
                <p class="text-sm text-gray-500">Manage product images</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>