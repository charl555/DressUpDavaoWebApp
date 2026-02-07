@props(['shops', 'search' => ''])

<div class="py-16 px-4 sm:px-6 lg:px-8 pt-[72px]">
    {{-- Header Section --}}
    <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-gray-900 mb-4 flex items-center justify-center">
            Our Partner Shops
        </h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Discover trusted rental shops across Davao City offering gowns and suits for every occasion
        </p>
    </div>

    {{-- Search --}}
    <div class="max-w-4xl mx-auto mb-12">
        <div class="flex justify-center">
            <div class="w-full sm:w-2/3 relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="shopSearch" name="search" value="{{ $search }}"
                    placeholder="Search shops by name or location..."
                    class="block w-full rounded-lg border-gray-300 pl-12 pr-4 py-3 text-gray-900 placeholder-gray-500 focus:border-purple-500 focus:ring-purple-500 text-base shadow-sm">
            </div>
        </div>
    </div>

    {{-- Loading Spinner --}}
    <div id="loadingSpinner" class="hidden flex justify-center items-center mb-8">
        <div class="w-8 h-8 border-4 border-purple-400 border-t-transparent rounded-full animate-spin"></div>
        <span class="ml-3 text-gray-600 font-medium">Searching shops...</span>
    </div>

    {{-- Shop List Section --}}
    <div id="shopList" class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        {{-- Render shop cards (replaced dynamically by AJAX) --}}
        @include('partials.shop-cards', ['shops' => $shops])
    </div>
</div>

{{-- AJAX Search Script (only for desktop) --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('shopSearch');
        const shopList = document.getElementById('shopList');
        const loadingSpinner = document.getElementById('loadingSpinner');
        let timer = null;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(timer);
                const query = this.value.trim();

                timer = setTimeout(() => {
                    // Show spinner
                    loadingSpinner.classList.remove('hidden');

                    fetch(`{{ route('shops.list') }}?search=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            shopList.innerHTML = data.html;
                        })
                        .catch(error => {
                            console.error('Search failed:', error);
                            shopList.innerHTML = `
                                <div class="col-span-full text-center py-12">
                                    <svg class="w-16 h-16 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Something went wrong</h3>
                                    <p class="text-gray-500">Please try again later.</p>
                                </div>`;
                        })
                        .finally(() => {
                            // Hide spinner when done
                            loadingSpinner.classList.add('hidden');
                        });
                }, 300); // debounce 300ms
            });
        }
    });
</script>