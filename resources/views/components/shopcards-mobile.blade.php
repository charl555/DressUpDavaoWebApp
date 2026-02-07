@props(['shops', 'search' => ''])

<div class="mobile-app-shops min-h-screen bg-gray-50">
    {{-- Mobile Header --}}
    <div class="sticky top-0 z-40 bg-white border-b border-gray-200 px-4 py-3 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <button onclick="window.history.back()" class="p-2 rounded-full hover:bg-gray-100 active:bg-gray-200">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <h1 class="text-lg font-semibold text-gray-900 playfair-display">Partner Shops</h1>

            <div class="w-10"></div> {{-- Spacer for alignment --}}
        </div>

        {{-- Search Bar with Form (no AJAX) --}}
        <form method="GET" action="{{ route('shops.list') }}" class="relative">
            <input type="hidden" name="app" value="1">
            <input type="hidden" name="mobile_nav" value="true">

            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <input type="text" name="search" value="{{ $search }}" placeholder="Search shops..."
                class="w-full pl-12 pr-4 py-3 bg-gray-100 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white focus:shadow-sm transition-all text-base"
                autocomplete="off">

            @if($search)
                <button type="button" onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </form>
    </div>

    {{-- Shop List --}}
    <div class="p-4 space-y-3">
        @if($shops->count() > 0)
            @foreach($shops as $shop)
                <a href="{{ route('shop.overview', $shop) }}?app=1&mobile_nav=true"
                    class="block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden active:scale-[0.98] transition-all duration-200">

                    {{-- Shop Header --}}
                    <div class="flex items-center p-4 border-b border-gray-100">
                        {{-- Shop Logo --}}
                        <div
                            class="w-16 h-16 rounded-xl overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 flex-shrink-0">
                            @if($shop->shop_logo)
                                <img src="{{ asset('uploads/' . $shop->shop_logo) }}" alt="{{ $shop->shop_name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Shop Info --}}
                        <div class="ml-4 flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-base line-clamp-1 mb-1">
                                {{ $shop->shop_name }}
                            </h3>

                            {{-- Rating --}}
                            <div class="flex items-center mb-2">
                                @php
                                    $averageRating = round($shop->shop_reviews->avg('rating'), 1);
                                    $reviewCount = $shop->shop_reviews->count();
                                @endphp
                                @if($reviewCount > 0)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-yellow-400 fill-current mr-0.5" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $averageRating }}
                                        </span>
                                        <span class="text-xs text-gray-500 ml-1">({{ $reviewCount }})</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 font-medium">No reviews yet</span>
                                @endif
                            </div>

                            {{-- Product Count --}}
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                {{ $shop->products->where('visibility', 'Yes')->count() }} products
                            </span>
                        </div>

                        {{-- View Arrow --}}
                        <div class="ml-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Shop Details --}}
                    <div class="p-4 pt-3">
                        {{-- Location --}}
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm text-gray-600 line-clamp-2">{{ $shop->shop_address }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div
                    class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No shops found</h3>
                <p class="text-gray-600 text-center mb-6 max-w-sm">
                    @if($search)
                        No results for "{{ $search }}"
                    @else
                        No shops available at the moment
                    @endif
                </p>
                @if($search)
                    <a href="{{ route('shops.list') }}?app=1&mobile_nav=true"
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 active:scale-95">
                        Clear Search
                    </a>
                @else
                    <a href="/?app=1&mobile_nav=true"
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 active:scale-95">
                        Back to Home
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // REMOVED: Focus search input on page load
        // This was causing the keyboard to open automatically
        // const searchInput = document.querySelector('input[name="search"]');
        // if (searchInput) {
        //     searchInput.focus();
        // }

        // Clear search function
        window.clearSearch = function () {
            window.location.href = '{{ route("shops.list") }}?app=1&mobile_nav=true';
        };

        // Auto-submit form when typing (optional, can remove if you prefer manual submit)
        let searchTimer = null;
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    this.form.submit();
                }, 800); // 800ms delay for auto-search
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

        // Alternative: Add a manual search button if you want to remove auto-submit
        // You could add this HTML to the form:
        // <button type="submit" class="hidden">Search</button>
        // And then add a search button in the UI if needed
    });
</script>

<style>
    .mobile-app-shops {
        -webkit-overflow-scrolling: touch;
        padding-top: env(safe-area-inset-top);
    }

    /* iOS-style active states */
    .mobile-app-shops a:active {
        transform: scale(0.98);
    }

    /* Fix for iOS input zoom */
    @media screen and (-webkit-min-device-pixel-ratio:0) {
        input[type="text"]:focus {
            font-size: 16px;
        }
    }
</style>