<div class="flex flex-col lg:flex-row pt-[20px] text-black justify-center max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
    <form id="filterForm" method="GET" action="{{ request()->url() }}" class="w-full lg:w-1/4 p-4 lg:pr-6 border-b lg:border-b-0 lg:border-r border-gray-200">
        <h2 class="text-2xl font-semibold mb-6 text-center lg:text-left text-black">Filter</h2>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2 text-black">Category</h3>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="category" value="all"
                        class="form-radio text-purple-600 focus:ring-purple-500"
                        {{ request('category', 'all') == 'all' ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-800">All</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="category" value="Gown"
                        class="form-radio text-purple-600 focus:ring-purple-500"
                        {{ request('category') == 'Gown' ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-800">Gowns</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="category" value="Suit"
                        class="form-radio text-purple-600 focus:ring-purple-500"
                        {{ request('category') == 'Suit' ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-800">Suits</span>
                </label>
            </div>
        </div>

        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-2 text-lg font-semibold text-black hover:text-purple-900 transition-colors">
                    <span>Type</span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>
                <div class="mt-2 space-y-2 pl-2">
                    @php
                        $subtypes = [
                            'Ball Gown', 'Wedding Gown', 'Prom Dress', 'Evening Gown', 'Cocktail Dress',
                            'Tuxedo', 'Business Suit', 'Formal Suit', 'Three-piece Suit'
                        ];
                        $selectedSubtypes = request('subtype', []);
                        if (!is_array($selectedSubtypes)) {
                            $selectedSubtypes = [$selectedSubtypes];
                        }
                    @endphp
                    @foreach($subtypes as $subtype)
                        <label class="flex items-center">
                            <input type="checkbox" name="subtype[]" value="{{ $subtype }}"
                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                {{ in_array($subtype, $selectedSubtypes) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-800">{{ $subtype }}</span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>

        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-2 text-lg font-semibold text-black hover:text-purple-900 transition-colors">
                    <span>Size</span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>
                <div class="mt-2 grid grid-cols-3 gap-2">
                    @php
                        // Size mapping: display name => database value
                        $sizeMapping = [
                            'XS' => 'Extra Small',
                            'S' => 'Small',
                            'M' => 'Medium',
                            'L' => 'Large',
                            'XL' => 'Extra Large',
                            'XXL' => 'Extra Extra Large'
                        ];
                        $selectedSizes = request('size', []);
                        if (!is_array($selectedSizes)) {
                            $selectedSizes = [$selectedSizes];
                        }
                    @endphp
                    @foreach($sizeMapping as $displaySize => $dbSize)
                        <label class="block">
                            <input type="checkbox" name="size[]" value="{{ $displaySize }}" class="hidden peer"
                                {{ in_array($displaySize, $selectedSizes) ? 'checked' : '' }}>
                            <span class="block w-full py-1 text-center border rounded-md cursor-pointer
                                        peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600
                                        hover:bg-purple-100 transition-colors text-gray-800">{{ $displaySize }}</span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>

        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-2 text-lg font-semibold text-black hover:text-purple-900 transition-colors">
                    <span>Color</span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>
                <div class="mt-2 grid grid-cols-4 gap-2">
                    @php
                        $colors = [
                            'Red' => 'bg-red-600',
                            'Blue' => 'bg-blue-600',
                            'Green' => 'bg-green-600',
                            'Black' => 'bg-black',
                            'White' => 'bg-white ring-1 ring-gray-300',
                            'Purple' => 'bg-purple-600',
                            'Pink' => 'bg-pink-500',
                            'Gold' => 'bg-yellow-500'
                        ];
                        $selectedColors = request('color', []);
                        if (!is_array($selectedColors)) {
                            $selectedColors = [$selectedColors];
                        }
                    @endphp
                    @foreach($colors as $colorName => $colorClass)
                        <label class="flex justify-center items-center">
                            <input type="checkbox" name="color[]" value="{{ $colorName }}" class="hidden peer"
                                {{ in_array($colorName, $selectedColors) ? 'checked' : '' }}>
                            <span
                                class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer
                                        {{ $colorClass }} peer-checked:border-purple-600 transition-all transform peer-checked:scale-110"
                                title="{{ $colorName }}"></span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>

        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-2 text-lg font-semibold text-black hover:text-purple-900 transition-colors">
                    <span>Occasion</span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>
                <div class="mt-2 space-y-2 pl-2">
                    @php
                        $occasions = ['Formal', 'Wedding', 'Prom', 'Business', 'Party', 'Graduation', 'Anniversary'];
                        $selectedOccasions = request('occasion', []);
                        if (!is_array($selectedOccasions)) {
                            $selectedOccasions = [$selectedOccasions];
                        }
                    @endphp
                    @foreach($occasions as $occasion)
                        <label class="flex items-center">
                            <input type="checkbox" name="occasion[]" value="{{ $occasion }}"
                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                {{ in_array($occasion, $selectedOccasions) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-800">{{ $occasion }}</span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>
        <div class="mb-6 pb-4">
            <label for="measurements-toggle" class="flex items-center justify-between cursor-pointer py-2">
                <span class="text-lg font-semibold text-black">Filter by Body Measurements</span>

                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                    <input type="checkbox" name="measurements_filter" id="measurements-toggle" value="1"
                        class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                        {{ request('measurements_filter') ? 'checked' : '' }}
                        @guest
                            onclick="event.preventDefault(); document.getElementById('signin-alert').classList.remove('hidden'); this.checked = false;"
                        @endguest />
                    <label for="measurements-toggle"
                        class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                </div>
            </label>

            <p class="text-sm text-gray-600 mt-2">
                Toggle to filter products based on your saved body measurements.
            </p>

            {{-- Styled alert with close button --}}
            @guest
                <div id="signin-alert" class="hidden mt-4 relative" role="alert">
                    <div class="border-s-4 border-red-700 bg-red-50 p-4 rounded">
                        <div class="flex items-start gap-2 text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-5 mt-0.5">
                                <path fill-rule="evenodd"
                                    d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                                    clip-rule="evenodd" />
                            </svg>

                            <div class="flex-1">
                                <strong class="font-medium"> Please sign in first </strong>
                                <p class="mt-1 text-sm text-red-700">
                                    You need to be signed in to filter products by your body measurements.
                                </p>
                            </div>

                            {{-- Close button --}}
                            <button type="button" onclick="document.getElementById('signin-alert').classList.add('hidden')"
                                class="text-red-700 hover:text-red-900">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            @endguest
        </div>


        <button type="submit"
            class="w-full mt-6 py-2 bg-purple-600 text-white font-semibold rounded-md hover:bg-purple-700 transition-colors duration-300">
            Apply Filters
        </button>
        <a href="{{ url()->current() }}"
            class="w-full mt-2 py-2 border border-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-100 transition-colors duration-300 block text-center">
            Clear Filters
        </a>
    </form>
    </form>

    <div class="flex flex-col flex-grow mt-8 lg:mt-0">
        <div class="flex flex-col sm:flex-row justify-between items-center px-4">
            <h1 class="text-4xl py-10 font-serif text-center sm:text-left">Browse Collections</h1>
        </div>
        <div
            class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
            @forelse ($products as $product)
                <div class="group cursor-pointer max-w-[300px] w-full"
                    onclick="window.location.href='{{ route('product.overview', ['product_id' => $product->product_id]) }}'">


                    <div
                        class="h-96 sm:h-[300px] md:h-[300px] lg:h-[300px] xl:h-[300px] w-full transform transition-transform duration-300 ease-in-out group-hover:-translate-y-1">
                        @php
                            $imageRecord = $product->product_images->first();
                           @endphp

                        @if ($imageRecord && $imageRecord->thumbnail_image)
                            <img src="{{ Storage::disk('public')->url($imageRecord->thumbnail_image) }}"
                                alt="{{ $product->name }}" class="h-full w-full object-cover rounded-md shadow-md" />
                        @else
                            <div
                                class="h-full w-full flex items-center justify-center bg-gray-300 text-gray-700 rounded-md shadow-md">
                                Image not available
                            </div>
                        @endif
                    </div>

                    <div class="mt-2 space-y-1">
                        <p
                            class="text-left text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors duration-300">
                            {{ $product->name }}
                        </p>

                        <p class="text-left text-gray-600 text-sm">{{ $product->type }}</p>
                        <p class="text-left text-gray-600 text-sm">{{ $product->subtype }}</p>
                        <p class="text-left text-gray-600 text-sm">{{ $product->size }}</p>
                        <p class="text-left text-gray-500 text-xs italic">{{ $product->user->shop->shop_name }}</p>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500">No products available.</p>
            @endforelse
        </div>


        <div class="px-4 mb-8">
            {{ $products->links() }}
        </div>


    </div>
</div>
</div>
</div>

<style>
    .toggle-checkbox:checked {
        right: 0;
        border-color: #8B5CF6;
        /* purple-600 */
    }

    .toggle-checkbox:checked+.toggle-label {
        background-color: #8B5CF6;
        /* purple-600 */
    }
</style>