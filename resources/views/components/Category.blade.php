<div class="py-20 bg-gradient-to-b from-gray-50 to-white px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4" style="font-family: 'Playfair Display', serif;">
                Browse Collections
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Discover the perfect outfit for your special occasion
            </p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 max-w-6xl mx-auto">
            <!-- Gowns Category -->
            <div
                class="group relative bg-white rounded-2xl overflow-hidden shadow-lg transform transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl cursor-pointer">
                <div class="relative aspect-[4/5] overflow-hidden">
                    <img src="{{ asset('frontend-images/gown-category.webp') }}"
                        data-src="{{ asset('frontend-images/gown-category.webp') }}" alt="Gowns Collection"
                        class="lazy absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                        loading="lazy" width="600" height="750">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-purple-900/20 to-indigo-900/20 group-hover:from-purple-900/30 group-hover:to-indigo-900/30 transition-all duration-300">
                    </div>

                    <!-- Content Overlay -->
                    <div class="absolute inset-x-0 bottom-0 p-8 text-white">
                        <div
                            class="transform group-hover:translate-y-0 translate-y-2 transition-transform duration-300">
                            <h2 class="text-4xl font-bold mb-2" style="font-family: 'Playfair Display', serif;">Gowns
                            </h2>
                            <p class="text-lg text-gray-200 mb-6">Wedding, Evening & Formal Attire</p>
                            <div
                                class="flex items-center space-x-2 text-white group-hover:text-purple-200 transition-colors duration-300">
                                <span class="font-semibold text-lg">Explore Collection</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Hover Effect Border -->
                    <div
                        class="absolute inset-0 border-2 border-transparent group-hover:border-purple-400/30 rounded-2xl transition-all duration-300">
                    </div>
                </div>
            </div>

            <!-- Suits Category -->
            <div
                class="group relative bg-white rounded-2xl overflow-hidden shadow-lg transform transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl cursor-pointer">
                <div class="relative aspect-[4/5] overflow-hidden">
                    <img src="{{ asset('frontend-images/suit-category.webp') }}"
                        data-src="{{ asset('frontend-images/suit-category.webp') }}" alt="Suits Collection"
                        class="lazy absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                        loading="lazy" width="600" height="750">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-purple-900/20 to-indigo-900/20 group-hover:from-purple-900/30 group-hover:to-indigo-900/30 transition-all duration-300">
                    </div>

                    <!-- Content Overlay -->
                    <div class="absolute inset-x-0 bottom-0 p-8 text-white">
                        <div
                            class="transform group-hover:translate-y-0 translate-y-2 transition-transform duration-300">
                            <h2 class="text-4xl font-bold mb-2" style="font-family: 'Playfair Display', serif;">Suits
                            </h2>
                            <p class="text-lg text-gray-200 mb-6">Tuxedos, Business & Casual Wear</p>
                            <div
                                class="flex items-center space-x-2 text-white group-hover:text-purple-200 transition-colors duration-300">
                                <span class="font-semibold text-lg">Explore Collection</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Hover Effect Border -->
                    <div
                        class="absolute inset-0 border-2 border-transparent group-hover:border-purple-400/30 rounded-2xl transition-all duration-300">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center mt-12">
            <a href="/product-list"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                View All Products
            </a>
        </div>
    </div>
</div>