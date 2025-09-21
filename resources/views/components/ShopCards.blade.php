{{-- <div class="pt-[200px] px-[400]">
    @forelse ($shops as $shop)
    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm mb-4 border border-gray-200">
        <div class="flex-shrink-0">
            @if ($shop->shop_logo)
            <img src="{{ asset('storage/shop-images/' . $shop->shop_logo) }}" alt="{{ $shop->shop_name }}"
                class="h-16 w-16 rounded-full object-cover" />
            @else
            <div class="h-16 w-16 flex items-center justify-center bg-gray-300 text-gray-700 rounded-full">
                No Logo
            </div>
            @endif
        </div>


        <div class="ml-4 flex-grow">
            <div class="font-semibold text-lg text-gray-900">{{ $shop->shop_name }}</div>
            <div class="text-sm text-gray-500">{{ $shop->shop_address }}</div>
            <div class="text-sm text-gray-500">
                {{ $shop->products()->where('visibility', 'Yes')->count() }} Products
            </div>
        </div>

        <div class="ml-auto">
            <a href="{{ route('shop.overview', $shop) }}">
                <button
                    class="bg-violet-600 text-white font-semibold py-2 px-6 rounded-md shadow-sm hover:bg-violet-500 transition duration-150 ease-in-out">
                    View Shop
                </button>
            </a>
        </div>
    </div>
    @empty
    <p class="text-center text-gray-500">No shops available.</p>
    @endforelse
</div> --}}
<div class="py-16 px-4 sm:px-6 lg:px-8 pt-[200px]">
    {{-- Header Section --}}
    <div class="text-center mb-12">
        <h2 class="text-4xl font-bold tracking-tight text-gray-950 font-playfair-display">
            Our Partner Shops
        </h2>
        <p class="mt-2 text-base text-gray-600">
            Discover trusted rental shops across Davao City offering premium gowns
            and suits for every occasion
        </p>
    </div>

    {{-- Search and Filter --}}
    <div x-data="{ open: false }" class="max-w-4xl mx-auto mb-10">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
            {{-- Search Bar --}}
            <div class="w-full sm:w-2/3 relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" placeholder="Search shops by name or location..."
                    class="block w-full rounded-lg border-gray-300 pl-10 pr-4 py-2 text-gray-900 placeholder-gray-400 focus:border-violet-600 focus:ring-violet-600 sm:text-sm">
            </div>

            {{-- Filter Button --}}
            <button @click="open = !open"
                class="w-full sm:w-1/3 flex items-center justify-center rounded-lg border border-gray-300 py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-600">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                    <path fill-rule="evenodd"
                        d="M2.628 1.603a.75.75 0 00-.918 1.442l.53.284A6.75 6.75 0 001.278 8.441a.75.75 0 001.45.396 5.25 5.25 0 0110.151.782.75.75 0 001.443-.396 6.75 6.75 0 00-1.745-6.22l.53-.284a.75.75 0 00-.918-1.442L10.3 2.59a.75.75 0 00-.6 0L2.628 1.603z"
                        clip-rule="evenodd" />
                    <path
                        d="M6.924 14.881a.75.75 0 00-.712 1.458 3.75 3.75 0 01-5.072-3.811.75.75 0 00-.918-1.442.75.75 0 00.918 1.442l.53-.284A5.25 5.25 0 003.75 16.5a5.25 5.25 0 008.205-5.912.75.75 0 00-1.45-.396 3.75 3.75 0 01-5.581 4.789z" />
                </svg>
                Filters
            </button>
        </div>

        {{-- Dynamic Filter Dropdown --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="mt-4 p-6 bg-white rounded-lg shadow-md border border-gray-200 w-1/2">
            <h3 class="text-sm font-medium text-gray-900">Filter Options</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                {{-- Location Filter --}}
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <select id="location" name="location"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-600 focus:ring-violet-600 sm:text-sm">
                        <option value="">All Locations</option>
                        <option value="Davao City">Davao City</option>
                        <option value="Toril">Toril</option>
                        <option value="Davao del Sur">Davao del Sur</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Shop List Section --}}
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($shops as $shop)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                @if ($shop->shop_logo)
                    <img src="{{ asset('public/storage/shop-images/' . $shop->shop_logo) }}" alt="{{ $shop->shop_name }}"
                        class="w-full h-48 object-cover object-center">
                @else
                    <div class="w-full h-48 flex items-center justify-center bg-gray-300 text-gray-700">
                        No Image
                    </div>
                @endif
                <div class="p-4 flex flex-col justify-between h-[150px]">
                    <h3 class="text-lg font-semibold text-gray-900 font-playfair-display">{{ $shop->shop_name }}</h3>
                    <div class="flex items-center text-gray-500 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>

                        <span class="text-sm">{{ $shop->shop_address }}</span>
                    </div>
                    {{-- <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        @foreach ($shop->specialties as $specialty)
                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full">{{ $specialty }}</span>
                        @endforeach
                    </div> --}}
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-sm text-gray-500">{{ $shop->products()->where('visibility', 'Yes')->count() }}
                            Products</span>
                        <a href="{{ route('shop.overview', $shop) }}">
                            <button class="text-sm font-semibold text-violet-600 hover:text-violet-500">
                                View
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 col-span-full">No shops available.</p>
        @endforelse
    </div>
</div>