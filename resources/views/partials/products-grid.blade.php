<!-- Product Grid Container -->
<div id="products-container">
    <!-- Skeleton Loading Grid (Hidden by default) -->
    <div id="skeleton-loading" class="hidden">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
            @for($i = 0; $i < 8; $i++)
                <div class="w-full animate-pulse">
                    <!-- Image skeleton - Responsive height -->
                    <div class="h-64 sm:h-72 md:h-80 lg:h-[400px] xl:h-[400px] w-full bg-gray-300 rounded-md shadow-sm sm:shadow-md"></div>
                    <!-- Content skeleton -->
                    <div class="mt-3 sm:mt-4 space-y-1 sm:space-y-2">
                        <div class="h-4 sm:h-5 bg-gray-300 rounded w-3/4"></div>
                        <div class="h-3 sm:h-4 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-3 sm:h-4 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-2 sm:h-3 bg-gray-200 rounded w-2/3"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Actual Products Grid -->
    <div id="products-grid">
        <div class="flex-grow grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
            @forelse ($products as $product)
                <div class="group cursor-pointer w-full relative"
                    onclick="window.location.href='{{ route('product.overview', ['product_id' => $product->product_id]) }}'">

                    <!-- Recommendation Badge - Responsive -->
                    @if(auth()->check() && $product->fit_score > 0)
                        <div class="absolute top-2 left-2 z-10">
                            @include('partials.recommendation-badge', [
                                'recommendation' => $product->recommendation_level,
                                'fitScore' => $product->fit_score,
                                'responsive' => true
                            ])
                        </div>
                    @endif

                    <!-- 3D Model Icon - Responsive -->
                    @if($product->product_3d_models && $product->product_3d_models->count() > 0)
                        <div class="absolute top-2 right-2 z-10">
                            <div class="relative group/3d">
                                <!-- 3D Model Icon - Smaller on mobile -->
                                <div class="bg-purple-600 text-white p-1.5 sm:p-2 rounded-full shadow-sm sm:shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-xl hover:bg-gray-800">
                                    <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                                    </svg>
                                </div>
                                
                                <!-- Tooltip - Hidden on mobile, shown on larger screens -->
                                <div class="hidden sm:block absolute right-0 top-full mt-2 w-48 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 opacity-0 invisible group-hover/3d:opacity-100 group-hover/3d:visible transition-all duration-300 transform translate-y-1 group-hover/3d:translate-y-0 z-20 shadow-xl">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold">3D Model Available</span>
                                    </div>
                                    <p class="mt-1 text-gray-300">View this product in 3D</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Product Image Container - Responsive -->
                    <div class="relative h-64 sm:h-72 md:h-80 lg:h-[400px] xl:h-[400px] w-full transform transition-transform duration-300 ease-in-out group-hover:-translate-y-1 bg-gray-100 rounded-md shadow-sm sm:shadow-md overflow-hidden">
                        @php
                            $imageRecord = $product->product_images->first();
                        @endphp

                        @if ($imageRecord && $imageRecord->thumbnail_image)
                            <div class="h-full w-full flex items-center justify-center p-3 sm:p-4">
                                <img src="{{ asset('uploads/' . $imageRecord->thumbnail_image) }}" alt="{{ $product->name }}"
                                    class="max-w-full max-h-full object-contain rounded-md" 
                                    loading="lazy" />
                            </div>
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-300 text-gray-700 rounded-md">
                                <div class="text-center p-2">
                                    <svg class="w-8 h-8 sm:w-12 sm:h-12 mx-auto text-gray-400 mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs sm:text-sm">No Image</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Product Details - Responsive -->
                    <div class="mt-2 sm:mt-3 space-y-0.5 sm:space-y-1">
                        <p class="text-left text-sm sm:text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors duration-300 line-clamp-1">
                            {{ $product->name }}
                        </p>
                        <p class="text-left text-xs sm:text-sm text-gray-600 line-clamp-1">{{ $product->subtype }}</p>
                        <p class="text-left text-xs sm:text-sm text-gray-600">{{ $product->size }}</p>
                        
                        @if(!$product->has_actual_measurements)
                            <div class="inline-flex items-center px-1.5 py-0.5 sm:px-2 sm:py-1 bg-gray-100 text-gray-600 text-xs rounded-full mt-0.5 sm:mt-1">
                                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 mr-0.5 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <span class="text-[10px] sm:text-xs">No measurements</span>
                            </div>
                        @endif
                        
                        <p class="text-left text-[10px] sm:text-xs text-gray-500 italic line-clamp-1">{{ $product->user->shop->shop_name }}</p>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500 text-sm sm:text-base py-8">No products available.</p>
            @endforelse
        </div>
    </div>

    <!-- Updated Pagination with AJAX - Responsive -->
    @if ($products->hasPages())
        <div class="mt-6 sm:mt-8 flex justify-center">
            <nav class="flex items-center gap-1 sm:gap-2" id="products-pagination">
                {{-- Previous Page Link --}}
                @if ($products->onFirstPage())
                    <span class="px-3 py-1.5 sm:px-4 sm:py-2 text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $products->previousPageUrl() }}" data-page="{{ $products->currentPage() - 1 }}"
                        class="px-3 py-1.5 sm:px-4 sm:py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 pagination-link text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($products->links()->elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-3 py-1.5 sm:px-4 sm:py-2 text-gray-500 bg-white border border-gray-200 rounded-lg text-sm sm:text-base">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $products->currentPage())
                                <span
                                    class="px-3 py-1.5 sm:px-4 sm:py-2 text-white bg-gradient-to-r from-purple-600 to-indigo-600 border border-purple-600 rounded-lg font-semibold text-sm sm:text-base">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" data-page="{{ $page }}"
                                    class="px-3 py-1.5 sm:px-4 sm:py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 pagination-link text-sm sm:text-base">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" data-page="{{ $products->currentPage() + 1 }}"
                        class="px-3 py-1.5 sm:px-4 sm:py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 pagination-link text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <span class="px-3 py-1.5 sm:px-4 sm:py-2 text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    @endif
</div>

<!-- Add responsive CSS for smooth transitions -->
<style>
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

    /* Line clamp utility for truncating text */
    .line-clamp-1 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
    }

    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle pagination clicks
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('pagination-link') ||
                e.target.closest('.pagination-link')) {
                e.preventDefault();

                const link = e.target.classList.contains('pagination-link')
                    ? e.target
                    : e.target.closest('.pagination-link');

                const url = link.getAttribute('href');

                if (url) {
                    loadProducts(url);
                }
            }
        });

        function loadProducts(url) {
            const skeletonLoading = document.getElementById('skeleton-loading');
            const productsGrid = document.getElementById('products-grid');
            const productsContainer = document.getElementById('products-container');

            // Disable all pagination links during loading
            const paginationLinks = document.querySelectorAll('.pagination-link');
            paginationLinks.forEach(link => {
                link.style.pointerEvents = 'none';
            });

            // Fade out current content and show skeleton
            productsGrid.classList.add('fade-out');

            setTimeout(() => {
                productsGrid.style.display = 'none';
                skeletonLoading.classList.remove('hidden');
                skeletonLoading.classList.add('fade-in');

                // Update URL without page reload
                if (history.pushState) {
                    const newUrl = new URL(url, window.location.origin);
                    window.history.pushState({ path: newUrl.href }, '', newUrl.href);
                }

                // AJAX request
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        // Create temporary container to parse HTML
                        const tempContainer = document.createElement('div');
                        tempContainer.innerHTML = html;

                        // Extract the new content
                        const newProductsGrid = tempContainer.querySelector('#products-grid');
                        const newPagination = tempContainer.querySelector('#products-pagination');

                        if (newProductsGrid) {
                            // Fade out skeleton and fade in new content
                            skeletonLoading.classList.add('fade-out');

                            setTimeout(() => {
                                skeletonLoading.classList.add('hidden');
                                skeletonLoading.classList.remove('fade-in', 'fade-out');

                                // Replace the content
                                productsGrid.innerHTML = newProductsGrid.innerHTML;
                                productsGrid.style.display = 'block';
                                productsGrid.classList.remove('fade-out');
                                productsGrid.classList.add('fade-in');

                                // Update pagination if it exists
                                if (newPagination) {
                                    document.querySelector('#products-pagination').innerHTML = newPagination.innerHTML;
                                }

                                // Re-enable pagination links
                                paginationLinks.forEach(link => {
                                    link.style.pointerEvents = 'auto';
                                });

                                // Scroll to top of products section smoothly
                                productsContainer.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }, 300);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading products:', error);
                        // Fallback to normal page load
                        window.location.href = url;
                    });
            }, 300);
        }

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function (e) {
            if (e.state && e.state.path) {
                loadProducts(e.state.path);
            } else {
                // If no state, reload the current page
                window.location.href = window.location.href;
            }
        });
    });
</script>