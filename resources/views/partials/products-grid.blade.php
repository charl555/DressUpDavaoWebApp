<!-- Product Grid Container -->
<div id="products-container">
    <!-- Skeleton Loading Grid (Hidden by default) -->
    <div id="skeleton-loading" class="hidden">
        <div
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
            @for($i = 0; $i < 8; $i++)
                <div class="max-w-[400px] w-full animate-pulse">
                    <!-- Image skeleton -->
                    <div
                        class="h-96 sm:h-[400px] md:h-[400px] lg:h-[400px] xl:h-[400px] w-full bg-gray-300 rounded-md shadow-md">
                    </div>
                    <!-- Content skeleton -->
                    <div class="mt-4 space-y-2">
                        <div class="h-5 bg-gray-300 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Actual Products Grid -->
    <div id="products-grid">
        <div
            class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
            @forelse ($products as $product)
                <div class="group cursor-pointer max-w-[400px] w-full relative"
                    onclick="window.location.href='{{ route('product.overview', ['product_id' => $product->product_id]) }}'">

                    <div
                        class="relative h-96 sm:h-[400px] md:h-[400px] lg:h-[400px] xl:h-[400px] w-full transform transition-transform duration-300 ease-in-out group-hover:-translate-y-1">
                        @php
                            $imageRecord = $product->product_images->first();
                        @endphp

                        @if ($imageRecord && $imageRecord->thumbnail_image)
                            <img src="{{ asset('uploads/' . $imageRecord->thumbnail_image) }}" alt="{{ $product->name }}"
                                class="h-full w-full object-cover rounded-md shadow-md" />
                        @else
                            <div
                                class="h-full w-full flex items-center justify-center bg-gray-300 text-gray-700 rounded-md shadow-md">
                                Image not available
                            </div>
                        @endif

                        @if ($product->status !== 'Available')
                            <div class="absolute inset-0 bg-gray-900/60 flex items-center justify-center rounded-md">
                                <span class="text-white text-lg font-semibold tracking-wide">Not Available</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-2 space-y-1">
                        <p
                            class="text-left text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors duration-300">
                            {{ $product->name }}
                        </p>
                        <p class="text-left text-gray-600 text-sm">{{ $product->subtype }}</p>
                        <p class="text-left text-gray-600 text-sm">{{ $product->size }}</p>
                        <p class="text-left text-gray-500 text-xs italic">{{ $product->user->shop->shop_name }}</p>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500">No products available.</p>
            @endforelse
        </div>
    </div>

    <!-- Updated Pagination with AJAX -->
    @if ($products->hasPages())
        <div class="mt-8 flex justify-center">
            <nav class="flex items-center gap-2" id="products-pagination">
                {{-- Previous Page Link --}}
                @if ($products->onFirstPage())
                    <span class="px-4 py-2 text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $products->previousPageUrl() }}" data-page="{{ $products->currentPage() - 1 }}"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 pagination-link">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($products->links()->elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-4 py-2 text-gray-500 bg-white border border-gray-200 rounded-lg">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $products->currentPage())
                                <span
                                    class="px-4 py-2 text-white bg-gradient-to-r from-purple-600 to-indigo-600 border border-purple-600 rounded-lg font-semibold">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" data-page="{{ $page }}"
                                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 pagination-link">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" data-page="{{ $products->currentPage() + 1 }}"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 pagination-link">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <span class="px-4 py-2 text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    @endif
</div>

<!-- Add this CSS for smooth transitions -->
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