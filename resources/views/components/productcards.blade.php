<div class="bg-gradient-to-b from-white to-gray-50 py-12 md:py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12 md:mb-16">
            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-3 md:mb-4"
                style="font-family: 'Playfair Display', serif;">
                Featured Collections
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                Discover our handpicked selection of premium fashion rentals
            </p>
        </div>

        <!-- Products Container -->
        <div id="products-container">
            <!-- Skeleton Loading Grid -->
            <div id="skeleton-loading" class="hidden">
                <div
                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6 mb-12 md:mb-16">
                    @for($i = 0; $i < 10; $i++)
                        <div class="animate-pulse">
                            <!-- Image skeleton -->
                            <div class="aspect-[3/4] bg-gray-300 rounded-xl shadow-sm mb-3"></div>
                            <!-- Content skeleton -->
                            <div class="space-y-2">
                                <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Actual Products Grid -->
            <div id="products-grid">
                <div
                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6 mb-12 md:mb-16">
                    @forelse ($products as $product)
                        <div class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer"
                            onclick="window.location.href='{{ route('product.overview', ['product_id' => $product->product_id]) }}'">

                            <!-- Product Image -->
                            <div class="relative aspect-[3/4] overflow-hidden bg-gray-200">
                                @php
                                    $imageRecord = $product->product_images->first();
                                    $imageUrl = null;

                                    if ($imageRecord && $imageRecord->thumbnail_image) {
                                        $imageUrl = asset('uploads/' . $imageRecord->thumbnail_image);
                                    }
                                @endphp

                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500 ease-out"
                                        loading="lazy"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    </div>
                                @endif

                                <!-- Fallback Image -->
                                <div
                                    class="{{ $imageUrl ? 'hidden' : 'flex' }} w-full h-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-500">
                                    <div class="text-center p-4">
                                        <svg class="w-8 h-8 md:w-10 md:h-10 mx-auto mb-1 md:mb-2 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs md:text-sm font-medium">Image Not Available</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="p-3 md:p-4">
                                <h3
                                    class="font-semibold text-sm md:text-base text-gray-900 mb-1 md:mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors duration-200">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs md:text-sm text-gray-600 font-medium capitalize">{{ $product->subtype }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 md:py-16">
                            <svg class="w-16 h-16 md:w-20 md:h-20 mx-auto text-gray-400 mb-3 md:mb-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                            </svg>
                            <h3 class="text-lg md:text-xl font-semibold text-gray-600 mb-1 md:mb-2">No Products Available
                            </h3>
                            <p class="text-gray-500 mb-4 md:mb-6 text-sm md:text-base">Check back later for new arrivals</p>
                            <a href="/product-list"
                                class="inline-flex items-center px-4 md:px-6 py-2 md:py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-md text-sm md:text-base">
                                Browse All Collections
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center">
            <a href="/product-list"
                class="inline-flex items-center px-6 py-3 md:px-8 md:py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 group text-sm md:text-base">
                <span class="mr-2 md:mr-3">View All Collections</span>
                <svg class="w-4 h-4 md:w-5 md:h-5 transform group-hover:translate-x-1 transition-transform duration-200"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    .fade-out {
        opacity: 0;
        transition: opacity 0.3s ease-out;
    }

    .fade-in {
        opacity: 1;
        transition: opacity 0.3s ease-in;
    }

    #products-grid {
        transition: opacity 0.3s ease;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // You can use this skeleton loading for initial page load if needed
        // For example, if you're loading products via AJAX initially

        // Example usage for initial loading:
        function showSkeletonLoading() {
            const skeletonLoading = document.getElementById('skeleton-loading');
            const productsGrid = document.getElementById('products-grid');

            productsGrid.style.display = 'none';
            skeletonLoading.classList.remove('hidden');
        }

        function hideSkeletonLoading() {
            const skeletonLoading = document.getElementById('skeleton-loading');
            const productsGrid = document.getElementById('products-grid');

            skeletonLoading.classList.add('hidden');
            productsGrid.style.display = 'block';
            productsGrid.classList.add('fade-in');
        }
        
        Call showSkeletonLoading() when starting to load products
        Call hideSkeletonLoading() when products are loaded
    });
</script>