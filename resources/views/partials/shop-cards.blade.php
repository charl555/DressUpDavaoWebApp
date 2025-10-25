@forelse ($shops as $shop)
    <div onclick="window.location.href='{{ route('shop.overview', $shop) }}'"
        class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden transform transition-all duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg cursor-pointer group">

        {{-- Shop Image --}}
        @if ($shop->shop_logo)
            <div class="relative overflow-hidden">
                <img src="{{ asset('storage/' . $shop->shop_logo) }}" alt="{{ $shop->shop_name }}"
                    class="w-full h-48 object-cover object-center group-hover:scale-105 transition-transform duration-300">
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </div>
        @else
            <div class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-600">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <p class="text-sm font-medium mt-2">No Image</p>
                </div>
            </div>
        @endif

        {{-- Card Body --}}
        <div class="p-6 flex flex-col justify-between min-h-[180px]">

            {{-- Shop Name --}}
            <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $shop->shop_name }}</h3>

            {{-- Rating Section --}}
            @php
                $averageRating = round($shop->shop_reviews->avg('rating'), 1);
                $reviewCount = $shop->shop_reviews->count();
            @endphp
            <div class="flex items-center mb-3">
                @if ($reviewCount > 0)
                    <div class="flex items-center space-x-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 {{ $i <= $averageRating ? 'text-yellow-400 fill-current' : 'text-gray-300' }}"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="text-sm text-gray-600 ml-2 font-medium">{{ $averageRating }} ({{ $reviewCount }})</span>
                @else
                    <span class="text-sm text-gray-400 font-medium">No reviews yet</span>
                @endif
            </div>

            {{-- Address --}}
            <div class="flex items-center text-gray-500 mb-4">
                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm text-gray-600 line-clamp-1">{{ $shop->shop_address }}</span>
            </div>

            {{-- Product Count + View --}}
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <span class="text-sm text-gray-500 font-medium">
                    {{ $shop->products()->where('visibility', 'Yes')->count() }} Products
                </span>
                <button
                    class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    View Shop
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="col-span-full text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-600 mb-2">No shops available</h3>
        <p class="text-gray-500">Check back later for new shop listings.</p>
    </div>
@endforelse

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>