<div class="bg-white border-b border-gray-200 pt-[200px]">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div
            class="bg-white rounded-lg shadow-sm p-6 md:p-8 flex flex-col md:flex-row items-center md:items-start gap-6">

            {{-- Shop Logo --}}
            <div class="flex-shrink-0">
                @if ($shop->shop_logo)
                    <img src="{{ asset('storage/shop-images/' . $shop->shop_logo) }}" alt="{{ $shop->shop_name }}"
                        class="h-24 w-24 md:h-32 md:w-32 rounded-full object-cover border border-gray-200 shadow-md" />
                @else
                    <div
                        class="h-24 w-24 md:h-32 md:w-32 flex items-center justify-center bg-gray-200 text-gray-600 rounded-full border border-gray-200 shadow-md">
                        No Logo
                    </div>
                @endif
            </div>

            {{-- Shop Info --}}
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl font-bold text-gray-900">{{ $shop->shop_name }}</h1>
                <p class="text-gray-500 mt-1">{{ $shop->shop_address }}</p>
                <p class="text-gray-600 mt-1">
                    {{ $shop->products()->where('visibility', 'Yes')->count() }} Products
                </p>

                {{-- Description --}}
                @if ($shop->shop_description)
                    <div class="mt-4">
                        <h2 class="text-lg font-semibold text-gray-800">About</h2>
                        <p class="text-gray-600 mt-1 leading-relaxed">
                            {{ $shop->shop_description }}
                        </p>
                    </div>
                @endif

                {{-- Policy --}}
                @if ($shop->shop_policy)
                    <div class="mt-4">
                        <h2 class="text-lg font-semibold text-gray-800">Shop Policy</h2>
                        <p class="text-gray-600 mt-1 leading-relaxed">
                            {{ $shop->shop_policy }}
                        </p>
                    </div>
                @endif

                {{-- Chat Button --}}
                @auth
                    @if (Auth::user()->role !== 'Admin' && Auth::user()->role !== 'SuperAdmin' && Auth::id() !== $shop->user_id)
                        <div class="mt-6">
                            <button id="startChatBtn" data-shop-owner-id="{{ $shop->user_id }}"
                                data-shop-name="{{ $shop->shop_name }}"
                                class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
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
    </div>
</div>

@auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startChatBtn = document.getElementById('startChatBtn');

            if (startChatBtn) {
                startChatBtn.addEventListener('click', async function () {
                    const shopOwnerId = this.dataset.shopOwnerId;
                    const shopName = this.dataset.shopName;

                    try {
                        // Send initial message to start conversation
                        const response = await fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                receiver_id: shopOwnerId,
                                message: `Hi! I'm interested in your shop "${shopName}". Can you help me with some questions?`
                            })
                        });

                        if (response.ok) {
                            // Open chat window if it exists
                            const openChatBtn = document.getElementById('openChatBtn');
                            if (openChatBtn) {
                                openChatBtn.click();

                                // Wait a moment for chat to load, then try to select the conversation
                                setTimeout(() => {
                                    const contactItems = document.querySelectorAll('.contact-item');
                                    const targetContact = Array.from(contactItems).find(item =>
                                        item.dataset.userId === shopOwnerId
                                    );
                                    if (targetContact) {
                                        targetContact.click();
                                    }
                                }, 1000);
                            }

                            // Update button to show success
                            this.innerHTML = `
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Chat Started!
                        `;
                            this.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                            this.classList.add('bg-green-600');
                            this.disabled = true;
                        } else {
                            throw new Error('Failed to start chat');
                        }
                    } catch (error) {
                        console.error('Error starting chat:', error);
                        alert('Failed to start chat. Please try again.');
                    }
                });
            }
        });
    </script>
@endauth