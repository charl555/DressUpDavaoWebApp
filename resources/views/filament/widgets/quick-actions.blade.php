<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Actions
        </x-slot>

        <x-slot name="description">
            Frequently used actions for managing your dress rental business
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Add New Product -->
            <a href="{{ route('filament.admin.resources.products.create') }}"
                class="flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg border border-blue-200 dark:border-blue-700 hover:shadow-lg transition-all duration-200 group">
                <div
                    class="flex items-center justify-center w-12 h-12 bg-blue-500 rounded-lg mb-3 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-plus class="w-6 h-6 text-white" />
                </div>
                <h3 class="font-semibold text-blue-900 dark:text-blue-100 text-center">Add Product</h3>
                <p class="text-sm text-blue-600 dark:text-blue-300 text-center mt-1">Add new dress or suit</p>
            </a>

            <!-- Create 3D Model -->
            <a href="{{ route('filament.admin.model-management.pages.create3d-models') }}"
                class="flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg border border-purple-200 dark:border-purple-700 hover:shadow-lg transition-all duration-200 group">
                <div
                    class="flex items-center justify-center w-12 h-12 bg-purple-500 rounded-lg mb-3 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-cube class="w-6 h-6 text-white" />
                </div>
                <h3 class="font-semibold text-purple-900 dark:text-purple-100 text-center">Create 3D Model</h3>
                <p class="text-sm text-purple-600 dark:text-purple-300 text-center mt-1">Generate 3D models</p>
            </a>

            <!-- View Rentals -->
            <a href="{{ route('filament.admin.resources.rentals.index') }}"
                class="flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg border border-green-200 dark:border-green-700 hover:shadow-lg transition-all duration-200 group">
                <div
                    class="flex items-center justify-center w-12 h-12 bg-green-500 rounded-lg mb-3 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-white" />
                </div>
                <h3 class="font-semibold text-green-900 dark:text-green-100 text-center">View Rentals</h3>
                <p class="text-sm text-green-600 dark:text-green-300 text-center mt-1">Manage bookings</p>
            </a>

            <!-- Manage Customers -->
            <a href="{{ route('filament.admin.resources.customers.index') }}"
                class="flex flex-col items-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-lg border border-orange-200 dark:border-orange-700 hover:shadow-lg transition-all duration-200 group">
                <div
                    class="flex items-center justify-center w-12 h-12 bg-orange-500 rounded-lg mb-3 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-users class="w-6 h-6 text-white" />
                </div>
                <h3 class="font-semibold text-orange-900 dark:text-orange-100 text-center">Customers</h3>
                <p class="text-sm text-orange-600 dark:text-orange-300 text-center mt-1">Manage customers</p>
            </a>
        </div>

        <!-- Additional Quick Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <x-heroicon-o-clock class="w-5 h-5 text-yellow-500 mr-2" />
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Pending Returns</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                    {{ \App\Models\Rentals::whereHas('product', function ($q) {
    $q->where('user_id', auth()->id()); })->where('rental_status', 'On Going')->count() }}
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 mr-2" />
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Overdue Returns</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                    {{ \App\Models\Rentals::whereHas('product', function ($q) {
    $q->where('user_id', auth()->id()); })->where('rental_status', 'On Going')->where('return_date', '<', now())->count() }}
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <x-heroicon-o-wrench-screwdriver class="w-5 h-5 text-blue-500 mr-2" />
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Maintenance Needed</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                    {{ \App\Models\Products::where('user_id', auth()->id())->where('maintenance_needed', true)->count() }}
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>