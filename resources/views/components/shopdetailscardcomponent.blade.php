<div class="bg-white border-b border-gray-200 pt-[80px] sm:pt-[200px]">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-lg shadow-sm p-6 md:p-8 ">
            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Left Column - Shop Identity & Basic Info --}}
                <div class="lg:col-span-1">
                    <div class="flex flex-col items-center lg:items-start space-y-6">

                        {{-- Shop Logo --}}
                        <div class="flex-shrink-0">
                            @if ($shop->shop_logo)
                                <img src="{{ asset('uploads/' . $shop->shop_logo) }}" alt="{{ $shop->shop_name }}"
                                    class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg" />
                            @else
                                <div
                                    class="h-32 w-32 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-600 rounded-full border-4 border-white shadow-lg">
                                    <span class="text-sm font-medium">No Logo</span>
                                </div>
                            @endif
                        </div>

                        {{-- Quick Stats --}}
                        <div class="text-center lg:text-left">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $shop->shop_name }}</h1>
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center justify-center lg:justify-start space-x-2 text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-sm">{{ $shop->shop_address }}</span>
                                </div>
                                <div class="flex items-center justify-center lg:justify-start space-x-2 text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                                    </svg>
                                    <span class="text-sm font-medium">
                                        {{ $shop->products()->where('visibility', 'Yes')->count() }} Products Available
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Review Summary --}}
                        <div class="w-full bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3 text-center lg:text-left">Customer
                                Reviews</h3>

                            @if ($shop->shop_reviews->count() > 0)
                                <div class="flex flex-col items-center lg:items-start space-y-3">
                                    <div class="flex items-center space-x-2">
                                        {{-- Average rating stars --}}
                                        @php
                                            $rounded = round($averageRating, 1);
                                            $filled = floor($rounded);
                                            $half = ($rounded - $filled) >= 0.5 ? 1 : 0;
                                            $empty = 5 - $filled - $half;
                                        @endphp

                                        <div class="flex items-center">
                                            @for ($i = 0; $i < $filled; $i++)
                                                <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.163c.969 0 1.371 1.24.588 1.81l-3.37 2.449a1 1 0 00-.364 1.118l1.285 3.955c.3.921-.755 1.688-1.54 1.118l-3.37-2.449a1 1 0 00-1.175 0l-3.37 2.449c-.785.57-1.84-.197-1.54-1.118l1.285-3.955a1 1 0 00-.364-1.118L2.51 9.382c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.955z" />
                                                </svg>
                                            @endfor
                                            @if ($half)
                                                <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <defs>
                                                        <linearGradient id="half">
                                                            <stop offset="50%" stop-color="currentColor" />
                                                            <stop offset="50%" stop-color="transparent" />
                                                        </linearGradient>
                                                    </defs>
                                                    <path fill="url(#half)"
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.163c.969 0 1.371 1.24.588 1.81l-3.37 2.449a1 1 0 00-.364 1.118l1.285 3.955c.3.921-.755 1.688-1.54 1.118l-3.37-2.449a1 1 0 00-1.175 0l-3.37 2.449c-.785.57-1.84-.197-1.54-1.118l1.285-3.955a1 1 0 00-.364-1.118L2.51 9.382c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.955z" />
                                                </svg>
                                            @endif
                                            @for ($i = 0; $i < $empty; $i++)
                                                <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 00.95.69h4.163c.969 0 1.371 1.24.588 1.81l-3.37 2.449a1 1 00-.364 1.118l1.285 3.955c.3.921-.755 1.688-1.54 1.118l-3.37-2.449a1 1 00-1.175 0l-3.37 2.449c-.785.57-1.84-.197-1.54-1.118l1.285-3.955a1 1 00-.364-1.118L2.51 9.382c-.783-.57-.38-1.81.588-1.81h4.163a1 1 00.95-.69l1.286-3.955z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-xl font-bold text-gray-800">{{ $rounded }}</span>
                                    </div>
                                    <button id="viewReviewsBtn"
                                        class="text-purple-600 hover:text-purple-800 font-medium text-sm">
                                        View all {{ $shop->shop_reviews->count() }} reviews
                                    </button>
                                </div>
                            @else
                                <p class="text-gray-500 text-center lg:text-left">No reviews yet</p>
                            @endif
                        </div>

                        {{-- Chat Button --}}
                        @auth
                            @if (Auth::user()->role !== 'Admin' && Auth::user()->role !== 'SuperAdmin' && Auth::id() !== $shop->user_id)
                                <div class="w-full">
                                    <button id="startChatBtn" data-shop-owner-id="{{ $shop->user_id }}"
                                        data-shop-name="{{ $shop->shop_name }}"
                                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 shadow-md">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                        Chat with Shop
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Right Column - Detailed Information --}}
                <div class="lg:col-span-2">
                    <div class="space-y-8">

                        {{-- Description Section --}}
                        @if ($shop->shop_description)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    About Our Shop
                                </h2>
                                <p class="text-gray-700 leading-relaxed">
                                    {{ $shop->shop_description }}
                                </p>
                            </div>
                        @endif

                        {{-- Policy & Payment Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Shop Policy --}}
                            @if ($shop->shop_policy)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Shop Policy
                                    </h2>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        {{ $shop->shop_policy }}
                                    </p>
                                </div>
                            @endif

                            {{-- Payment Options --}}
                            @if ($shop->payment_options)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Payment Options
                                    </h2>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        {{ $shop->payment_options }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Social Media Links --}}
                        @if ($shop->facebook_url || $shop->instagram_url || $shop->tiktok_url)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">

                                    Connect With Us
                                </h2>
                                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                                    @if ($shop->facebook_url)
                                        <a href="{{ $shop->facebook_url }}" target="_blank"
                                            class="flex items-center space-x-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M22 12.07C22 6.49 17.52 2 12 2S2 6.49 2 12.07c0 5 3.66 9.13 8.44 9.93v-7.03H8.08V12h2.36V9.79c0-2.33 1.39-3.62 3.52-3.62.99 0 2.03.18 2.03.18v2.24h-1.14c-1.12 0-1.47.7-1.47 1.41V12h2.5l-.4 2.97h-2.1v7.03C18.34 21.2 22 17.07 22 12.07z" />
                                            </svg>
                                            <span class="font-medium">Facebook</span>
                                        </a>
                                    @endif

                                    @if ($shop->instagram_url)
                                        <a href="{{ $shop->instagram_url }}" target="_blank"
                                            class="flex items-center space-x-2 px-4 py-2 bg-pink-50 text-pink-600 rounded-lg hover:bg-pink-100 transition-colors duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5zM12 7a5 5 0 1 1 0 10a5 5 0 0 1 0-10zm0 1.5A3.5 3.5 0 1 0 12 15a3.5 3.5 0 0 0 0-7zm5.25-.88a1.13 1.13 0 1 1 0 2.26a1.13 1.13 0 0 1 0-2.26z" />
                                            </svg>
                                            <span class="font-medium">Instagram</span>
                                        </a>
                                    @endif

                                    @if ($shop->tiktok_url)
                                        <a href="{{ $shop->tiktok_url }}" target="_blank"
                                            class="flex items-center space-x-2 px-4 py-2 bg-gray-50 text-gray-800 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M12.5 2c.35 2.18 1.99 3.99 4.12 4.63V9.5a6.41 6.41 0 0 1-3.18-.88v5.03a4.65 4.65 0 1 1-4.65-4.65c.23 0 .46.02.68.05v2.19a2.52 2.52 0 1 0 1.46 2.29V2h1.57z" />
                                            </svg>
                                            <span class="font-medium">TikTok</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reviews Modal (keep the same) --}}
<div id="reviewsModal" class="fixed inset-0 bg-gray-900/50 items-center justify-center z-[9999] hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="flex justify-between items-center border-b px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-800">Customer Reviews</h3>
            <button id="closeReviewsBtn" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
        </div>

        <div class="p-6 max-h-[70vh] overflow-y-auto space-y-5">
            @forelse ($reviews as $review)
                <div class="pb-4 border-b border-gray-100">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-medium text-gray-800">{{ $review->user->name }}</span>
                        <div class="flex items-center">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 {{ $i < $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.163c.969 0 1.371 1.24.588 1.81l-3.37 2.449a1 1 0 00-.364 1.118l1.285 3.955c.3.921-.755 1.688-1.54 1.118l-3.37-2.449a1 1 0 00-1.175 0l-3.37 2.449c-.785.57-1.84-.197-1.54-1.118l1.285-3.955a1 1 0 00-.364-1.118L2.51 9.382c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.955z" />
                                </svg>
                            @endfor
                        </div>
                    </div>

                    <div class="text-sm text-gray-500 mb-1">
                        <span class="font-medium text-gray-600">Bookings on this shop:</span>
                        {{ $review->booking_count ?? 1 }}
                    </div>

                    @if ($review->comment)
                        <p class="text-gray-700 mt-2 leading-snug">{{ $review->comment }}</p>
                    @endif

                    <div class="mt-2 text-xs text-gray-400">
                        @if ($review->updated_at->gt($review->created_at))
                            <span class="text-purple-500 font-medium">Updated Review</span> –
                            Updated on {{ $review->updated_at->format('F j, Y g:i A') }}
                        @else
                            Posted on {{ $review->created_at->format('F j, Y g:i A') }}
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center">No reviews available.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Keep the same JavaScript --}}
@auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startChatBtn = document.getElementById('startChatBtn');
            const viewBtn = document.getElementById('viewReviewsBtn');
            const modal = document.getElementById('reviewsModal');
            const closeBtn = document.getElementById('closeReviewsBtn');

            // Chat functionality (same as before)
            if (startChatBtn) {
                startChatBtn.addEventListener('click', async function () {
                    // ... existing chat code ...
                });
            }

            // Modal functionality (same as before)
            if (viewBtn && modal && closeBtn) {
                viewBtn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
                closeBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                });
            }
        });
    </script>
@endauth