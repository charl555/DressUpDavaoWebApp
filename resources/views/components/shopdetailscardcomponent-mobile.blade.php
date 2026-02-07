<div class="bg-white">
    {{-- Shop Header --}}
    <div class="relative">
        {{-- Shop Logo and Name --}}
        <div class="px-4 pt-6 pb-8">
            <div class="flex flex-col items-center text-center space-y-4">
                {{-- Shop Logo --}}
                <div class="relative">
                    @if ($shop->shop_logo)
                        <img src="{{ asset('uploads/' . $shop->shop_logo) }}" alt="{{ $shop->shop_name }}"
                            class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg" />
                    @else
                        <div
                            class="h-24 w-24 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-600 rounded-full border-4 border-white shadow-lg">
                            <span class="text-sm font-medium">No Logo</span>
                        </div>
                    @endif
                </div>

                {{-- Shop Name --}}
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $shop->shop_name }}</h1>

                    {{-- Location --}}
                    <div class="flex items-center justify-center space-x-1 text-gray-600 mb-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm line-clamp-1">{{ $shop->shop_address }}</span>
                    </div>

                    {{-- Products Count --}}
                    <div class="flex items-center justify-center space-x-1 text-gray-600">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                        </svg>
                        <span class="text-sm font-medium">
                            {{ $shop->products()->where('visibility', 'Yes')->count() }} Products Available
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chat Button (if authenticated) --}}
        @auth
            @if (Auth::user()->role !== 'Admin' && Auth::user()->role !== 'SuperAdmin' && Auth::id() !== $shop->user_id)
                <div class="px-4 mb-6">
                    <button id="mobileStartChatBtn" data-shop-owner-id="{{ $shop->user_id }}"
                        data-shop-name="{{ $shop->shop_name }}"
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg active:from-purple-700 active:to-indigo-700 transition-all duration-200 focus:outline-none shadow-md">
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

    {{-- Reviews Section --}}
    <div class="px-4 mb-6">
        <div class="bg-gray-50 rounded-xl p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Customer Reviews</h3>

            @if ($shop->shop_reviews->count() > 0)
                <div class="flex flex-col items-center space-y-3">
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
                                        <linearGradient id="half-mobile">
                                            <stop offset="50%" stop-color="currentColor" />
                                            <stop offset="50%" stop-color="transparent" />
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#half-mobile)"
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
                    <button id="mobileViewReviewsBtn" class="text-purple-600 active:text-purple-800 font-medium text-sm">
                        View all {{ $shop->shop_reviews->count() }} reviews
                    </button>
                </div>
            @else
                <p class="text-gray-500 text-center">No reviews yet</p>
            @endif
        </div>
    </div>

    {{-- Description Section --}}
    @if ($shop->shop_description)
        <div class="px-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    About Our Shop
                </h2>
                <p class="text-gray-700 leading-relaxed text-sm">
                    {{ $shop->shop_description }}
                </p>
            </div>
        </div>
    @endif

    {{-- Shop Policy & Payment Options --}}
    <div class="px-4 space-y-4 mb-6">
        @if ($shop->shop_policy)
            <div class="bg-gray-50 rounded-xl p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        @if ($shop->payment_options)
            <div class="bg-gray-50 rounded-xl p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="px-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Connect With Us</h2>
                <div class="flex flex-wrap justify-center gap-3">
                    @if ($shop->facebook_url)
                        <a href="{{ $shop->facebook_url }}" target="_blank"
                            class="flex items-center space-x-2 px-4 py-3 bg-blue-50 text-blue-600 rounded-lg active:bg-blue-100 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22 12.07C22 6.49 17.52 2 12 2S2 6.49 2 12.07c0 5 3.66 9.13 8.44 9.93v-7.03H8.08V12h2.36V9.79c0-2.33 1.39-3.62 3.52-3.62.99 0 2.03.18 2.03.18v2.24h-1.14c-1.12 0-1.47.7-1.47 1.41V12h2.5l-.4 2.97h-2.1v7.03C18.34 21.2 22 17.07 22 12.07z" />
                            </svg>
                            <span class="font-medium">Facebook</span>
                        </a>
                    @endif

                    @if ($shop->instagram_url)
                        <a href="{{ $shop->instagram_url }}" target="_blank"
                            class="flex items-center space-x-2 px-4 py-3 bg-pink-50 text-pink-600 rounded-lg active:bg-pink-100 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5zM12 7a5 5 0 1 1 0 10a5 5 0 0 1 0-10zm0 1.5A3.5 3.5 0 1 0 12 15a3.5 3.5 0 0 0 0-7zm5.25-.88a1.13 1.13 0 1 1 0 2.26a1.13 1.13 0 0 1 0-2.26z" />
                            </svg>
                            <span class="font-medium">Instagram</span>
                        </a>
                    @endif

                    @if ($shop->tiktok_url)
                        <a href="{{ $shop->tiktok_url }}" target="_blank"
                            class="flex items-center space-x-2 px-4 py-3 bg-gray-50 text-gray-800 rounded-lg active:bg-gray-100 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.5 2c.35 2.18 1.99 3.99 4.12 4.63V9.5a6.41 6.41 0 0 1-3.18-.88v5.03a4.65 4.65 0 1 1-4.65-4.65c.23 0 .46.02.68.05v2.19a2.52 2.52 0 1 0 1.46 2.29V2h1.57z" />
                            </svg>
                            <span class="font-medium">TikTok</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Mobile Reviews Modal --}}
    <div id="mobileReviewsModal" class="fixed inset-0 bg-gray-900/50 items-center justify-center z-[9999] hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[80vh] overflow-hidden">
            <div class="flex justify-between items-center border-b px-6 py-4 sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-800">Customer Reviews</h3>
                <button id="mobileCloseReviewsBtn" class="text-gray-500 active:text-gray-700 text-xl">&times;</button>
            </div>

            <div class="p-6 max-h-[calc(80vh-80px)] overflow-y-auto space-y-5">
                @forelse ($reviews as $review)
                    <div class="pb-4 border-b border-gray-100 last:border-b-0">
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
                            <p class="text-gray-700 mt-2 leading-snug text-sm">{{ $review->comment }}</p>
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
                    <p class="text-gray-500 text-center py-8">No reviews available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Mobile-specific JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mobile Reviews Modal
        const mobileViewBtn = document.getElementById('mobileViewReviewsBtn');
        const mobileModal = document.getElementById('mobileReviewsModal');
        const mobileCloseBtn = document.getElementById('mobileCloseReviewsBtn');

        if (mobileViewBtn && mobileModal && mobileCloseBtn) {
            mobileViewBtn.addEventListener('click', () => {
                mobileModal.classList.remove('hidden');
                mobileModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });

            mobileCloseBtn.addEventListener('click', () => {
                mobileModal.classList.add('hidden');
                mobileModal.classList.remove('flex');
                document.body.style.overflow = '';
            });

            mobileModal.addEventListener('click', (e) => {
                if (e.target === mobileModal) {
                    mobileModal.classList.add('hidden');
                    mobileModal.classList.remove('flex');
                    document.body.style.overflow = '';
                }
            });
        }

        // Mobile Chat Button - Simple implementation
        const mobileStartChatBtn = document.getElementById('mobileStartChatBtn');

        if (mobileStartChatBtn) {
            mobileStartChatBtn.addEventListener('click', function () {
                const shopOwnerId = this.dataset.shopOwnerId;
                const shopName = this.dataset.shopName;

                // Simple redirect to chat page
                window.location.href = `/chat?user_id=${shopOwnerId}&shop_name=${encodeURIComponent(shopName)}&app=1`;

                // Update button state
                this.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Opening Chat...
                `;
                this.classList.remove('from-purple-600', 'to-indigo-600');
                this.classList.add('bg-gray-600');
                this.disabled = true;
            });
        }
    });
</script>