<div class="flex flex-col lg:flex-row pt-[20px] text-black justify-center max-w-screen-xxl mx-auto px-4 sm:px-6 lg:px-8">
    <form id="filterForm" method="GET" class="w-full lg:w-1/4 p-6 bg-white rounded-lg shadow-sm border border-gray-100 lg:mr-6">
        <h2 class="text-2xl font-bold mb-6 text-center lg:text-left text-gray-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
            </svg>
            Filter Products
        </h2>

        <!-- Type Filter - Gender Based -->
        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Type
                    </span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>
                <div class="mt-3 space-y-3 pl-2">
                    @php
                        // Determine user gender and set appropriate subtypes
                        $userGender = auth()->check() ? auth()->user()->gender : null;
                        
                        if ($userGender === 'Female') {
                            $subtypes = [
                                'Ball Gown', 'Wedding Gown', 'Prom Dress', 'Evening Gown', 'Cocktail Dress',    
                                'A-line Gown', 'Sheath Gown', 'Mermaid Gown', 'Off-shoulder Gown', 'Princess Gown',
                                'Empire Waist Gown', 'V-neck Gown', 'Trumpet Gown', 
                            ];
                        } elseif ($userGender === 'Male') {
                            $subtypes = [
                                'Tuxedo', 'Business Suit', 'Formal Suit', 'Three-piece Suit',   
                                'Two Piece Suit', 'Italian Suit', 'Single Breasted Suit', 'Double Breasted Suit',
                                'Casual Suit', 'Denim Suit', 'Leather Suit', 'Bomber Jacket', 'Blazer'
                            ];
                        } else {
                            // For guests or users with other/prefer not to say gender, show all
                            $subtypes = [
                                'Ball Gown', 'Wedding Gown', 'Prom Dress', 'Evening Gown', 'Cocktail Dress',
                                'A-line Gown', 'Sheath Gown', 'Mermaid Gown', 'Off-shoulder Gown', 'Princess Gown',
                                'Empire Waist Gown', 'V-neck Gown', 'Trumpet Gown',
                                'Tuxedo', 'Business Suit', 'Formal Suit', 'Three-piece Suit',
                                'Two Piece Suit', 'Italian Suit', 'Single Breasted Suit', 'Double Breasted Suit',
                                'Casual Suit', 'Denim Suit', 'Leather Suit', 'Bomber Jacket', 'Blazer'
                            ];
                        }
                        
                        $selectedSubtypes = request('subtype', []);
                        if (!is_array($selectedSubtypes)) {
                            $selectedSubtypes = [$selectedSubtypes];
                        }
                    @endphp
                    @foreach($subtypes as $subtype)
                        <label class="flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" name="subtype[]" value="{{ $subtype }}"
                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500 filter-input"
                                {{ in_array($subtype, $selectedSubtypes) ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-700 font-medium">{{ $subtype }}</span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>

        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                        Size
                    </span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>
                <div class="mt-3 grid grid-cols-3 gap-3">
                    @php
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
                            <input type="checkbox" name="size[]" value="{{ $displaySize }}" class="hidden peer filter-input"
                                {{ in_array($displaySize, $selectedSizes) ? 'checked' : '' }}>
                            <span class="block w-full py-2 text-center border-2 border-gray-200 rounded-lg cursor-pointer font-medium
                                        peer-checked:bg-gradient-to-r peer-checked:from-purple-600 peer-checked:to-indigo-600 
                                        peer-checked:text-white peer-checked:border-transparent
                                        hover:border-purple-300 transition-all duration-200 text-gray-700">
                                {{ $displaySize }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>

        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Color
                    </span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>
                <div class="mt-3 grid grid-cols-4 gap-3">
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
                            <input type="checkbox" name="color[]" value="{{ $colorName }}" class="hidden peer filter-input"
                                {{ in_array($colorName, $selectedColors) ? 'checked' : '' }}>
                            <span
                                class="w-10 h-10 rounded-full border-3 border-transparent cursor-pointer shadow-sm
                                        {{ $colorClass }} peer-checked:border-purple-600 peer-checked:ring-2 peer-checked:ring-purple-200 transition-all transform peer-checked:scale-110 hover:scale-105"
                                title="{{ $colorName }}"></span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>

        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                        Occasion
                    </span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>
                <div class="mt-3 space-y-3 pl-2">
                    @php
                        $occasions = ['Formal', 'Wedding', 'Prom', 'Business', 'Party', 'Graduation', 'Anniversary', 'Debut', 'Gala'];
                        $selectedOccasions = request('occasion', []);
                        if (!is_array($selectedOccasions)) {
                            $selectedOccasions = [$selectedOccasions];
                        }
                    @endphp
                    @foreach($occasions as $occasion)
                        <label class="flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" name="occasion[]" value="{{ $occasion }}"
                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500 filter-input"
                                {{ in_array($occasion, $selectedOccasions) ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-700 font-medium">{{ $occasion }}</span>
                        </label>
                    @endforeach
                </div>
            </details>
        </div>

        <!-- Body Measurements Filter -->
        @if(auth()->guest() || (auth()->check() && auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin'))
            <div class="mb-6 pb-4">
                <label for="measurements-toggle" class="flex items-center justify-between cursor-pointer py-3 bg-gray-50 rounded-lg px-4">
                    <span class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Body Measurements
                    </span>

                    <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="measurements_filter" id="measurements-toggle" value="1"
                        class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-gray-300 appearance-none cursor-pointer filter-input
                               checked:right-0 checked:border-purple-600 transition-all duration-300"
                        {{ request('measurements_filter') ? 'checked' : '' }}
                        @guest
                            onclick="event.preventDefault(); document.getElementById('signin-alert').classList.remove('hidden'); this.checked = false;"
                        @endguest />
                        <label for="measurements-toggle"
                            class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></label>
                    </div>
                </label>

                <p class="text-sm text-gray-600 mt-2 pl-2">
                    Filter products based on your saved body measurements
                </p>

                <!-- Sign-in Alert for Guests -->
                @guest
                    <div id="signin-alert" class="hidden mt-4" role="alert">
                        <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded-lg">
                            <div class="flex items-start gap-3 text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-5 mt-0.5 flex-shrink-0">
                                    <path fill-rule="evenodd"
                                        d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                                        clip-rule="evenodd" />
                                </svg>

                                <div class="flex-1">
                                    <strong class="font-medium">Sign In Required</strong>
                                    <p class="mt-1 text-sm">
                                        Please sign in to filter by your body measurements.
                                    </p>
                                </div>

                                <!-- Close Button -->
                                <button type="button" onclick="document.getElementById('signin-alert').classList.add('hidden')"
                                    class="text-red-700 hover:text-red-900 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex space-x-4">
            <button type="button" id="clearFiltersBtn"
                class="flex-1 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                Clear All
            </button>
            <button type="submit"
                class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Apply
            </button>
        </div>
    </form>

    <div class="flex flex-col flex-grow mt-8 lg:mt-0">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
            <h1 class="text-4xl font-bold text-gray-800 text-center sm:text-left font-serif">Browse Collections</h1>
            <p class="text-gray-600 mt-2 text-center sm:text-left">Discover the perfect outfit for every occasion</p>
        </div>
    
        <!-- Products Grid -->
        <div id="productsContainer">
            @include('partials.products-grid', ['products' => $products])
        </div>
    </div>
</div>

<style>
    .toggle-checkbox:checked {
        right: 0;
        border-color: #8B5CF6;
    }

    .toggle-checkbox:checked + .toggle-label {
        background-color: #8B5CF6;
    }

    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #8B5CF6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Custom scrollbar for filter section */
    .filter-section::-webkit-scrollbar {
        width: 6px;
    }

    .filter-section::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .filter-section::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 10px;
    }

    .filter-section::-webkit-scrollbar-thumb:hover {
        background: #8b5cf6;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const productsContainer = document.getElementById('productsContainer');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const filterInputs = document.querySelectorAll('.filter-input');
    
    let isLoading = false;
    let currentRequest = null;

    // Update URL without page reload
    function updateURL(params) {
        const url = new URL(window.location);
        url.search = new URLSearchParams(params).toString();
        window.history.replaceState({}, '', url);
    }

    // Get form data including checkboxes
    function getFormData() {
        const formData = new FormData(filterForm);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (data[key]) {
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
        
        // Handle checkboxes properly
        filterInputs.forEach(input => {
            if (input.type === 'checkbox') {
                if (input.checked) {
                    if (input.name.endsWith('[]')) {
                        if (!data[input.name] || !Array.isArray(data[input.name])) {
                            data[input.name] = [];
                        }
                        if (!data[input.name].includes(input.value)) {
                            data[input.name].push(input.value);
                        }
                    } else {
                        data[input.name] = input.value;
                    }
                } else if (input.name.endsWith('[]') && data[input.name] && Array.isArray(data[input.name])) {
                    data[input.name] = data[input.name].filter(val => val !== input.value);
                    if (data[input.name].length === 0) {
                        delete data[input.name];
                    }
                } else if (!input.checked && data[input.name] === input.value) {
                    delete data[input.name];
                }
            }
        });

        return data;
    }

    // Load products via AJAX
    function loadProducts() {
        if (isLoading) {
            if (currentRequest) {
                currentRequest.abort();
            }
        }

        isLoading = true;
        
        const formData = getFormData();
        
        // Update URL
        updateURL(formData);
        
        // AJAX request
        currentRequest = new XMLHttpRequest();
        currentRequest.open('GET', '{{ route("product.list") }}?' + new URLSearchParams(formData).toString());
        currentRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        currentRequest.onload = function() {
            if (currentRequest.status === 200) {
                productsContainer.innerHTML = currentRequest.responseText;
            } else {
                console.error('Error loading products');
                productsContainer.innerHTML = '<div class="col-span-full text-center text-red-500 py-8">Error loading products. Please try again.</div>';
            }
            
            isLoading = false;
            currentRequest = null;
        };
        
        currentRequest.onerror = function() {
            console.error('Request failed');
            isLoading = false;
            currentRequest = null;
        };
        
        currentRequest.send();
    }

    // Debounce function to prevent too many requests
    let debounceTimer;
    function debounceLoadProducts() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(loadProducts, 500);
    }

    // Add event listeners to filter inputs
    filterInputs.forEach(input => {
        input.addEventListener('change', debounceLoadProducts);
    });

    // Clear filters button
    clearFiltersBtn.addEventListener('click', function() {
        // Reset form
        filterForm.reset();
        
        // Clear URL parameters
        window.history.replaceState({}, '', window.location.pathname);
        
        // Reload products
        loadProducts();
    });

    // Prevent form submission (we're using AJAX)
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        loadProducts();
    });

});
</script>