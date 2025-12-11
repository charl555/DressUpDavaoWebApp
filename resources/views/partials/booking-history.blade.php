@if ($bookings->isEmpty())
    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
        <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p class="text-sm font-medium mb-2">You don't have any bookings yet.</p>
        <a href="{{ route('product.list') }}"
            class="mt-4 px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-md">
            Browse Products
        </a>
    </div>
@else
    <div id="bookings-table-container">
        <div class="overflow-x-auto shadow-sm rounded-xl border border-gray-100">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700">Booking Date</th>
                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700">Item</th>
                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700">Shop</th>
                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700">Address</th>
                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700">Status</th>
                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookings-table-body">
                    @foreach ($bookings as $booking)
                        <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6 font-semibold text-gray-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('F j, Y') }}
                            </td>
                            <td class="py-4 px-6 text-gray-800">
                                {{ $booking->product->name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-gray-800">
                                {{ $booking->product->user->shop->shop_name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-gray-800">
                                {{ $booking->product->user->shop->shop_address ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'Confirmed' => 'bg-green-100 text-green-800',
                                        'Cancelled' => 'bg-red-100 text-red-800',
                                        'Completed' => 'bg-gray-100 text-gray-800',
                                        'On Going' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $badgeClass = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($booking->status === 'Completed')
                                    @php
                                        $existingReview = \App\Models\ShopReviews::where('user_id', auth()->id())
                                            ->where('shop_id', $booking->product->user->shop->shop_id)
                                            ->first();
                                    @endphp

                                    @if (!$existingReview)
                                        <button
                                            onclick="openReviewModal('{{ $booking->product->user->shop->shop_id }}', '{{ $booking->product->user->shop->shop_name }}', false)"
                                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-sm">
                                            Leave Review
                                        </button>
                                    @else
                                        <button
                                            onclick="openReviewModal('{{ $booking->product->user->shop->shop_id }}', '{{ $booking->product->user->shop->shop_name }}', true)"
                                            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg font-semibold hover:bg-gray-200 transition-all duration-300">
                                            Update Review
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center" id="bookings-pagination">
            <nav class="flex items-center gap-2">
                {{-- Previous Page Link --}}
                @if ($bookings->onFirstPage())
                    <span class="px-4 py-2 text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $bookings->previousPageUrl() }}" data-page="{{ $bookings->currentPage() - 1 }}"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 pagination-link">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($bookings->links()->elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-4 py-2 text-gray-500 bg-white border border-gray-200 rounded-lg">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $bookings->currentPage())
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
                @if ($bookings->hasMorePages())
                    <a href="{{ $bookings->nextPageUrl() }}" data-page="{{ $bookings->currentPage() + 1 }}"
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
    </div>
@endif