<!-- Enhanced Hero Component -->
<div class="flex w-full bg-gradient-to-br from-gray-900 to-black pt-[72px]">
    <div class="relative w-full h-[500px] lg:h-[700px] overflow-hidden">
        <!-- Background Image with Gradient Overlay -->
        <div class="absolute inset-0">
            <img src="{{ asset('frontend-images/gown-backdrop.webp') }}"
                data-src="{{ asset('frontend-images/gown-backdrop.webp') }}" alt="Fashion Rental"
                class="lazy absolute inset-0 w-full h-full object-cover bg-cover bg-center transform scale-105 group-hover:scale-100 transition-transform duration-700 ease-out"
                loading="eager">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-900/40 to-indigo-900/30"></div>
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-white text-4xl lg:text-6xl xl:text-7xl font-bold mb-6 leading-tight"
                    style="font-family: 'Playfair Display', serif;">
                    Wear the Moment.<br>
                    <span class="bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent">
                        Rent with Ease.
                    </span>
                </h1>
                <p class="text-gray-200 text-lg lg:text-xl mb-8 max-w-2xl mx-auto leading-relaxed">
                    Discover premium fashion rentals from trusted vendors across Davao.
                    Perfect outfits for every occasion, delivered with excellence.
                </p>
                <a href="/product-list"
                    class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 ease-out group border-0">
                    <span class="mr-3">View All Collections</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-10">
            <div class="animate-bounce">
                <svg class="w-6 h-6 text-white opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>
        </div>
    </div>
</div>