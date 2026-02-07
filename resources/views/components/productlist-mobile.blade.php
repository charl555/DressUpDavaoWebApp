@props(['products'])

<div class="mobile-app-products min-h-screen bg-gray-50">
    {{-- Mobile Header --}}
    <div class="sticky top-0 z-40 bg-white border-b border-gray-200 px-4 py-3 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <button onclick="window.history.back()" class="p-2 rounded-full hover:bg-gray-100 active:bg-gray-200">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <h1 class="text-lg font-semibold text-gray-900 playfair-display">Collections</h1>

            {{-- Filter Button --}}
            <button id="filterButton" class="p-2 rounded-full hover:bg-gray-100 active:bg-gray-200">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
            </button>
        </div>

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('product.list') }}" class="relative">
            <input type="hidden" name="app" value="1">
            <input type="hidden" name="mobile_nav" value="true">
            
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search products..."
                class="w-full pl-12 pr-4 py-3 bg-gray-100 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white focus:shadow-sm transition-all text-base"
                autocomplete="off">
            
            @if(request('search'))
                <button type="button" onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </form>
    </div>

    {{-- Filter Sidebar (Hidden by default) --}}
    <div id="filterSidebar" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden">
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-sm bg-white shadow-xl overflow-y-auto">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Filters</h2>
                    <button id="closeFilter" class="p-2 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                {{-- Simple Filters Form --}}
                <form id="mobileFilterForm" method="GET" action="{{ route('product.list') }}">
                    <input type="hidden" name="app" value="1">
                    <input type="hidden" name="mobile_nav" value="true">
                    
                    {{-- Type Filter --}}
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Type
                        </h3>
                        <div class="space-y-2">
                            @php
                                $types = ['Gown', 'Suit'];
                                $selectedTypes = request('type', []);
                                if (!is_array($selectedTypes)) {
                                    $selectedTypes = [$selectedTypes];
                                }
                            @endphp
                            @foreach($types as $type)
                                <label class="flex items-center p-3 rounded-lg hover:bg-purple-50 transition-colors cursor-pointer">
                                    <input type="checkbox" name="type[]" value="{{ $type }}"
                                        class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                        {{ in_array($type, $selectedTypes) ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700">{{ $type }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Style Filter --}}
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Style
                        </h3>
                        <input type="text" name="subtype_search" placeholder="Search styles..."
                            class="w-full p-3 mb-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            value="{{ request('subtype') }}">
                        <div class="max-h-60 overflow-y-auto space-y-2">
                            @php
                                $selectedSubtypes = request('subtype', []);
                                if (!is_array($selectedSubtypes)) {
                                    $selectedSubtypes = [$selectedSubtypes];
                                }
                            @endphp
                            @foreach(['A-line Gown', 'Mermaid Gown', 'Ball Gown', 'Trumpet Gown', 'Evening Gown', 'Classic Suit', 'Tuxedo'] as $subtype)
                                <label class="flex items-center p-3 rounded-lg hover:bg-purple-50 transition-colors cursor-pointer">
                                    <input type="checkbox" name="subtype[]" value="{{ $subtype }}"
                                        class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                        {{ in_array($subtype, $selectedSubtypes) ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700 text-sm">{{ $subtype }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Size Filter --}}
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                            Size
                        </h3>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                                @php
                                    $selectedSizes = request('size', []);
                                    if (!is_array($selectedSizes)) {
                                        $selectedSizes = [$selectedSizes];
                                    }
                                @endphp
                                <label class="flex flex-col items-center p-3 rounded-lg hover:bg-purple-50 transition-colors cursor-pointer">
                                    <input type="checkbox" name="size[]" value="{{ $size }}"
                                        class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                        {{ in_array($size, $selectedSizes) ? 'checked' : '' }}>
                                    <span class="mt-2 text-gray-700 text-sm">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4">
                        <div class="flex space-x-3">
                            <button type="button" onclick="clearFilters()"
                                class="flex-1 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                                Clear All
                            </button>
                            <button type="submit"
                                class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Products Grid --}}
    <div class="p-4">
        @if($products->count() > 0)
            <div class="grid grid-cols-2 gap-3">
                @foreach($products as $product)
                    <a href="{{ route('product.overview', ['product_id' => $product->product_id]) }}?app=1&mobile_nav=true"
                        class="block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden active:scale-[0.98] transition-all duration-200">
                        
                        {{-- Product Image --}}
                        <div class="relative h-48 bg-gray-100">
                            @php
                                $imageRecord = $product->product_images->first();
                            @endphp

                            @if($imageRecord && $imageRecord->thumbnail_image)
                                <img src="{{ asset('uploads/' . $imageRecord->thumbnail_image) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Recommendation Badge --}}
                            @if(auth()->check() && $product->fit_score > 0)
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $product->recommendation_level === 'Perfect Fit' ? 'bg-green-100 text-green-800' : 
                                           ($product->recommendation_level === 'Great Fit' ? 'bg-blue-100 text-blue-800' : 
                                           'bg-yellow-100 text-yellow-800') }}">
                                        {{ $product->fit_score }}%
                                    </span>
                                </div>
                            @endif

                            {{-- 3D Model Indicator --}}
                            @if($product->product_3d_models && $product->product_3d_models->count() > 0)
                                <div class="absolute top-2 right-2 bg-purple-600 text-white p-1.5 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Product Info --}}
                        <div class="p-3">
                            <h3 class="font-semibold text-gray-900 text-sm line-clamp-1 mb-1">
                                {{ $product->name }}
                            </h3>
                            <p class="text-xs text-gray-600 mb-2">{{ $product->subtype }}</p>
                            
                            @if(!$product->has_actual_measurements)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    No measurements
                                </span>
                            @endif
                            
                            <p class="text-xs text-gray-500 mt-2">{{ $product->user->shop->shop_name }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="mt-6 flex justify-center">
                    <nav class="flex items-center gap-2">
                        {{-- Previous Page Link --}}
                        @if($products->onFirstPage())
                            <span class="px-4 py-2 text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}?app=1&mobile_nav=true"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        {{-- Current Page --}}
                        <span class="px-4 py-2 text-white bg-gradient-to-r from-purple-600 to-indigo-600 border border-purple-600 rounded-lg font-semibold text-sm">
                            {{ $products->currentPage() }}
                        </span>

                        {{-- Next Page Link --}}
                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}?app=1&mobile_nav=true"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <span class="px-4 py-2 text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div
                    class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600 text-center mb-6 max-w-sm">
                    Try adjusting your filters or search term
                </p>
                <a href="{{ route('product.list') }}?app=1&mobile_nav=true"
                    class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 active:scale-95">
                    Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Filter sidebar functionality
        const filterButton = document.getElementById('filterButton');
        const filterSidebar = document.getElementById('filterSidebar');
        const closeFilter = document.getElementById('closeFilter');
        const mobileFilterForm = document.getElementById('mobileFilterForm');

        if (filterButton && filterSidebar) {
            filterButton.addEventListener('click', () => {
                filterSidebar.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });

            closeFilter.addEventListener('click', () => {
                filterSidebar.classList.add('hidden');
                document.body.style.overflow = '';
            });

            // Close sidebar when clicking outside
            filterSidebar.addEventListener('click', (e) => {
                if (e.target === filterSidebar) {
                    filterSidebar.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        }

        // Clear search function
        window.clearSearch = function () {
            window.location.href = '{{ route("product.list") }}?app=1&mobile_nav=true';
        };

        // Clear all filters
        window.clearFilters = function () {
            window.location.href = '{{ route("product.list") }}?app=1&mobile_nav=true';
        };

        // Auto-submit form on filter change (optional)
        const filterInputs = mobileFilterForm?.querySelectorAll('input[type="checkbox"]');
        if (filterInputs) {
            filterInputs.forEach(input => {
                input.addEventListener('change', () => {
                    // Add a small delay to allow multiple selections
                    setTimeout(() => {
                        mobileFilterForm.submit();
                    }, 300);
                });
            });
        }

        // Handle pull-to-refresh
        let touchStartY = 0;
        document.addEventListener('touchstart', function (e) {
            if (window.scrollY === 0) {
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        document.addEventListener('touchmove', function (e) {
            if (touchStartY === 0) return;

            const touchY = e.touches[0].clientY;
            const diff = touchY - touchStartY;

            // Pull down to refresh (100px threshold)
            if (window.scrollY === 0 && diff > 100) {
                location.reload();
            }
        }, { passive: true });
    });
</script>

<style>
    .mobile-app-products {
        -webkit-overflow-scrolling: touch;
        padding-top: env(safe-area-inset-top);
    }

    /* iOS-style active states */
    .mobile-app-products a:active {
        transform: scale(0.98);
    }

    /* Fix for iOS input zoom */
    @media screen and (-webkit-min-device-pixel-ratio:0) {
        input[type="text"]:focus {
            font-size: 16px;
        }
    }

    /* Smooth transitions for filter sidebar */
    #filterSidebar > div {
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
        }
        to {
            transform: translateX(0);
        }
    }
</style>