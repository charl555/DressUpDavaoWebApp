<div
    class="flex flex-col lg:flex-row pt-[200px] text-black justify-center max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full lg:w-1/4 p-4 lg:pr-6 border-b lg:border-b-0 lg:border-r border-gray-200">
        <h2 class="text-2xl font-semibold mb-6 text-center lg:text-left text-black">Filter</h2>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2 text-black">Category</h3>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="category" value="all"
                        class="form-radio text-purple-600 focus:ring-purple-500" checked>
                    <span class="ml-2 text-gray-800">All</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="category" value="gowns"
                        class="form-radio text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-gray-800">Gowns</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="category" value="suits"
                        class="form-radio text-purple-600 focus:ring-purple-500">
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
                    <label class="flex items-center">
                        <input type="checkbox" name="subtype" value="ball-gown"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Ball Gown</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="subtype" value="wedding-gown"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Wedding Gown</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="subtype" value="prom-dress"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Prom Dress</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="subtype" value="tuxedo"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Tuxedo</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="subtype" value="suit-set"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Suit Set</span>
                    </label>
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
                    <label class="flex justify-center items-center">
                        <input type="checkbox" name="color" value="red" class="hidden peer">
                        <span
                            class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer
                                    bg-red-600 peer-checked:border-purple-600 transition-all transform peer-checked:scale-110"
                            title="Red"></span>
                    </label>
                    <label class="flex justify-center items-center">
                        <input type="checkbox" name="color" value="blue" class="hidden peer">
                        <span
                            class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer
                                    bg-blue-600 peer-checked:border-purple-600 transition-all transform peer-checked:scale-110"
                            title="Blue"></span>
                    </label>
                    <label class="flex justify-center items-center">
                        <input type="checkbox" name="color" value="green" class="hidden peer">
                        <span
                            class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer
                                    bg-green-600 peer-checked:border-purple-600 transition-all transform peer-checked:scale-110"
                            title="Green"></span>
                    </label>
                    <label class="flex justify-center items-center">
                        <input type="checkbox" name="color" value="black" class="hidden peer">
                        <span
                            class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer
                                    bg-black peer-checked:border-purple-600 transition-all transform peer-checked:scale-110"
                            title="Black"></span>
                    </label>
                    <label class="flex justify-center items-center">
                        <input type="checkbox" name="color" value="white" class="hidden peer">
                        <span
                            class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer
                                    bg-white peer-checked:border-purple-600 transition-all transform peer-checked:scale-110 ring-1 ring-gray-300"
                            title="White"></span>
                    </label>
                    <label class="flex justify-center items-center">
                        <input type="checkbox" name="color" value="lavender" class="hidden peer">
                        <span
                            class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer
                                    bg-purple-300 peer-checked:border-purple-600 transition-all transform peer-checked:scale-110"
                            title="Lavender"></span>
                    </label>
                    <label class="flex justify-center items-center">
                        <input type="checkbox" name="color" value="gold" class="hidden peer">
                        <span
                            class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer
                                    bg-yellow-500 peer-checked:border-purple-600 transition-all transform peer-checked:scale-110"
                            title="Gold"></span>
                    </label>
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
                    <label class="flex items-center">
                        <input type="checkbox" name="occasion" value="formal"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Formal</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="occasion" value="wedding"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Wedding</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="occasion" value="prom"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Prom</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="occasion" value="business"
                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-2 text-gray-800">Business</span>
                    </label>
                </div>
            </details>
        </div>
        <div class="mb-6 pb-4">
            <label for="measurements-toggle" class="flex items-center justify-between cursor-pointer py-2">
                <span class="text-lg font-semibold text-black">Filter by Body Type</span>
                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                    <input type="checkbox" name="measurements_filter" id="measurements-toggle"
                        class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                    <label for="measurements-toggle"
                        class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                </div>
            </label>
            <p class="text-sm text-gray-600 mt-2">
                Toggle to filter products based on your saved body type.
            </p>
        </div>
        <button
            class="w-full mt-6 py-2 bg-purple-600 text-white font-semibold rounded-md hover:bg-purple-700 transition-colors duration-300">
            Apply Filters
        </button>
        <button
            class="w-full mt-2 py-2 border border-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-100 transition-colors duration-300">
            Clear Filters
        </button>
    </div>

    <div class="flex flex-col flex-grow mt-8 lg:mt-0">
        <div class="flex flex-col sm:flex-row justify-between items-center px-4">
            <h1 class="text-4xl py-10 font-serif text-center sm:text-left">Browse Collections</h1>
            <div class="relative mb-4 sm:mb-0">
                <select
                    class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-md shadow leading-tight focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    <option>Sort by: Featured</option>
                    <option>Sort by: Newest Arrivals</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                    </svg>
                </div>
            </div>
        </div>
        <div
            class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
            @forelse ($products as $product)
                <div class="group cursor-pointer max-w-[300px] w-full">
                    <div
                        class="h-96 sm:h-[300px] md:h-[300px] lg:h-[300px] xl:h-[300px] w-full transform transition-transform duration-300 ease-in-out group-hover:-translate-y-1">

                        @php

                            $imageRecord = $product->product_images->first();
                        @endphp

                        @if ($imageRecord && $imageRecord->thumbnail_image)
                            <img src="{{ asset('uploads/' . $imageRecord->thumbnail_image) }}"
                                alt="{{ $product->name }}" class="h-full w-full object-cover rounded-md shadow-md" />
                        @else
                            <div
                                class="h-full w-full flex items-center justify-center bg-gray-300 text-gray-700 rounded-md shadow-md">
                                Image not available
                            </div>
                        @endif
                    </div>

                    <div class="mt-2">
                        <p
                            class="text-left text-black text-xl font-semibold transition-colors duration-300 ease-in-out group-hover:text-purple-600">
                            {{ $product->name }}
                        </p>
                        <p class="text-left text-gray-600 text-base">{{ $product->type }}</p>
                        <p class="text-left text-gray-600 text-base">{{ $product->subtype }}</p>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500">No products available.</p>
            @endforelse
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