<div class="bg-gradient-to-b from-white to-gray-50 py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-gray-900 mb-4" style="font-family: 'Playfair Display', serif;">
                Featured Collections
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Discover our handpicked selection of premium fashion rentals
            </p>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8 mb-16">
            @forelse ($products as $product)
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer"
                    onclick="window.location.href='{{ route('product.overview', ['product_id' => $product->product_id]) }}'">

                    <!-- Product Image -->
                    <div class="relative aspect-[3/4] overflow-hidden bg-gray-200">
                        @php
                            $imageRecord = $product->product_images->first();
                            $imageUrl = null;

                            if ($imageRecord && $imageRecord->thumbnail_image) {
                                $imageUrl = asset('storage/' . $imageRecord->thumbnail_image);
                            }
                        @endphp

                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500 ease-out"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif

                        <!-- Fallback Image -->
                        <div
                            class="{{ $imageUrl ? 'hidden' : 'flex' }} w-full h-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-500">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm font-medium">Image Not Available</p>
                            </div>
                        </div>

                        {{-- <!-- Status Badge -->
                        <div class="absolute top-3 left-3">
                            @php
                            $statusColors = [
                            'Available' => 'bg-green-500',
                            'Rented' => 'bg-yellow-500',
                            'Reserved' => 'bg-blue-500',
                            'Unavailable' => 'bg-red-500',
                            ];
                            $statusColor = $statusColors[$product->status] ?? 'bg-gray-500';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold text-white {{ $statusColor }}">
                                {{ $product->status }}
                            </span>
                        </div> --}}
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3
                            class="font-semibold text-lg text-gray-900 mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors duration-200">
                            {{ $product->name }}
                        </h3>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600 font-medium capitalize">{{ $product->subtype }}</span>

                        </div>
                        {{-- <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $product->user->shop->shop_id->shop_name ?? 'No Shop' }}
                        </div> --}}
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Products Available</h3>
                    <p class="text-gray-500 mb-6">Check back later for new arrivals</p>
                    <a href="/product-list"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-md">
                        Browse All Collections
                    </a>
                </div>
            @endforelse
        </div>

        <!-- CTA Section -->
        <div class="text-center">
            <a href="/product-list"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 group">
                <span class="mr-3">View All Collections</span>
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>