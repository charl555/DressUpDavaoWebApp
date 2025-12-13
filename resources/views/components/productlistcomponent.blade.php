<div
    class="flex flex-col lg:flex-row pt-[20px] text-black justify-center max-w-screen-xxl mx-auto px-4 sm:px-6 lg:px-8">
    <form id="filterForm" method="GET"
        class="w-full lg:w-1/4 p-6 bg-white rounded-lg shadow-sm border border-gray-100 lg:mr-6">
        <h2 class="text-2xl font-bold mb-6 text-center lg:text-left text-gray-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
            </svg>
            Filter Products
        </h2>

        @guest
            <!-- Type Filter -->
            <div class="mb-6 border-b border-gray-200 pb-4">
                <details class="group">
                    <summary
                        class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
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
                            $types = ['Gown', 'Suit'];
                            $selectedTypes = request('type', []);
                            if (!is_array($selectedTypes)) {
                                $selectedTypes = [$selectedTypes];
                            }
                        @endphp
                        @foreach($types as $type)
                            <label
                                class="flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                                <input type="checkbox" name="type[]" value="{{ $type }}"
                                    class="form-checkbox text-purple-600 rounded focus:ring-purple-500 filter-input" {{ in_array($type, $selectedTypes) ? 'checked' : '' }}>
                                <span class="ml-3 text-gray-700 font-medium">{{ $type }}</span>
                            </label>
                        @endforeach
                    </div>
                </details>
            </div>
        @endguest

        <!-- Subtype Filter -->
        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Style
                    </span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>

                <!-- Search Box for Subtypes -->
                <div class="mt-4 mb-3 px-2">
                    <div class="relative">
                        <input type="text" id="subtypeSearch" placeholder="Search styles..."
                            class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                            onkeyup="filterSubtypes()">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button type="button" onclick="clearSubtypeSearch()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 flex justify-between">
                        <span id="subtypeVisibleCount">Showing all styles</span>
                        <button type="button" onclick="selectAllSubtypes()"
                            class="text-purple-600 hover:text-purple-800 text-xs font-medium">
                            Select All
                        </button>
                    </div>
                </div>

                <div id="subtypeList" class="mt-3 space-y-3 pl-2 max-h-96 overflow-y-auto pr-2">
                    @php
                        // Comprehensive gown list from your data
                        $gownTypes = [
                            'A-line Gown',
                            'Anarkali Gown',
                            'Angel Sleeve Gown',
                            'Asymmetrical Gown',
                            'Backless Gown',
                            'Ball Gown',
                            'Bandage Gown',
                            'Bandeau Gown',
                            'Beaded Gown',
                            'Bias-cut Gown',
                            'Boat Neck Gown',
                            'Bodysuit Gown',
                            'Boho Gown',
                            'Bolero Gown',
                            'Bouffant Gown',
                            'Bridesmaid Gown',
                            'Brocade Gown',
                            'Bustier Gown',
                            'Cape Gown',
                            'Cap-sleeve Gown',
                            'Cargo Gown',
                            'Chantilly Lace Gown',
                            'Chemise Gown',
                            'Chiffon Gown',
                            'Choker Neck Gown',
                            'Classic Column Gown',
                            'Cleopatra Gown',
                            'Clover Neck Gown',
                            'Cocktail Gown',
                            'Cold-shoulder Gown',
                            'Column Gown',
                            'Contrast Panel Gown',
                            'Convertible Gown',
                            'Court Train Gown',
                            'Cowl Neck Gown',
                            'Crepe Gown',
                            'Cut-out Gown',
                            'Debutante Gown',
                            'Deep V-neck Gown',
                            'Denim Gown',
                            'Detachable Train Gown',
                            'Diagonal Neck Gown',
                            'Draped Gown',
                            'Drop Waist Gown',
                            'Duster Gown',
                            'Embellished Gown',
                            'Embossed Gown',
                            'Empire Waist Gown',
                            'Envelope Neck Gown',
                            'Evening Gown',
                            'Fabric Wrap Gown',
                            'Faille Gown',
                            'Faux Wrap Gown',
                            'Feathered Gown',
                            'Fitted Gown',
                            'Flared Gown',
                            'Flipper Sleeve Gown',
                            'Filipiniana Gown',
                            'Flounce Gown',
                            'Flyaway Gown',
                            'Formal Maxi Gown',
                            'Frilled Gown',
                            'Gathered Gown',
                            'Georgette Gown',
                            'Glitter Gown',
                            'Godet Gown',
                            'Grecian Gown',
                            'Guest Gown',
                            'Halter Gown',
                            'Handkerchief Hem Gown',
                            'High-collar Gown',
                            'High-low Gown',
                            'High-neck Gown',
                            'Hourglass Gown',
                            'Illusion Gown',
                            'Jabot Gown',
                            'Jersey Gown',
                            'Jumpsuit Gown',
                            'Kaftan Gown',
                            'Keyhole Back Gown',
                            'Keyhole Neck Gown',
                            'Kimono Gown',
                            'Knit Gown',
                            'Lace Gown',
                            'Layered Gown',
                            'Lehenga Gown',
                            'Linen Gown',
                            'Long Sleeve Gown',
                            'Mermaid Gown',
                            'Mesh Gown',
                            'Metallic Gown',
                            'Military Gown',
                            'Modest Gown',
                            'Mother of the Bride Gown',
                            'Mousseline Gown',
                            'Nehru Collar Gown',
                            'Night Gown',
                            'Off-shoulder Gown',
                            'Ombré Gown',
                            'One-shoulder Gown',
                            'Organza Gown',
                            'Overlay Gown',
                            'Panel Gown',
                            'Peasant Gown',
                            'Peek-a-boo Gown',
                            'Pencil Gown',
                            'Peplum Gown',
                            'Peter Pan Collar Gown',
                            'Pinafore Gown',
                            'Pleated Gown',
                            'Plunge Neck Gown',
                            'Polo Neck Gown',
                            'Princess Gown',
                            'Prom Gown',
                            'Quilted Gown',
                            'Racerback Gown',
                            'Raglan Sleeve Gown',
                            'Raja Poshak Gown',
                            'Ravissant Gown',
                            'Rhinestone Gown',
                            'Ribbon Gown',
                            'Robe Gown',
                            'Ruching Gown',
                            'Ruffle Gown',
                            'Sari Gown',
                            'Satin Gown',
                            'Scalloped Gown',
                            'Sequin Gown',
                            'Sequined Gown',
                            'Set-in Sleeve Gown',
                            'Shantung Gown',
                            'Sheath Gown',
                            'Shiffon Gown',
                            'Shirred Gown',
                            'Shirtwaist Gown',
                            'Silk Gown',
                            'Skater Gown',
                            'Slip Gown',
                            'Smocked Gown',
                            'Spaghetti Strap Gown',
                            'Strapless Gown',
                            'Surplice Gown',
                            'Sweetheart Gown',
                            'Taffeta Gown',
                            'Tea-length Gown',
                            'Teddy Gown',
                            'Tented Gown',
                            'Tie-back Gown',
                            'Tie-dye Gown',
                            'Tiered Gown',
                            'Trapeze Gown',
                            'Trumpet Gown',
                            'Tulle Gown',
                            'Tunic Gown',
                            'Turtleneck Gown',
                            'Two-piece Gown',
                            'Velvet Gown',
                            'Venetian Gown',
                            'V-neck Gown',
                            'Watteau Train Gown',
                            'Wedding Gown',
                            'Wrap Gown',
                            'Yoke Gown',
                            'Other',
                        ];

                        // Comprehensive suit list from your data
                        $suitTypes = [
                            '2-Button Suit',
                            '3-Button Suit',
                            '4-Button Suit',
                            '6-Button Suit',
                            'Admiral Suit',
                            'Athletic Fit Suit',
                            'Balmacaan Coat Suit',
                            'Banana Republic Suit',
                            'Barong Tagalog',
                            'Basketweave Suit',
                            'Beach Suit',
                            'Biker Jacket Suit',
                            'Black Tie Suit',
                            'Blazer Suit',
                            'Boating Blazer Suit',
                            'Bold Stripe Suit',
                            'Bomber Jacket Suit',
                            'Bond Suit',
                            'Business Suit',
                            'Café Racer Suit',
                            'Canvas Suit',
                            'Cape Suit',
                            'Cap-toe Suit',
                            'Cardigan Suit',
                            'Casual Suit',
                            'Chalk Stripe Suit',
                            'Chesterfield Coat Suit',
                            'Chino Suit',
                            'Classic Fit Suit',
                            'Clean Front Suit',
                            'Clergy Suit',
                            'Corduroy Suit',
                            'Country Suit',
                            'Court Suit',
                            'Cricket Blazer Suit',
                            'Cropped Suit',
                            'Cruise Suit',
                            'Custom Suit',
                            'Cutaway Coat',
                            'Day Suit',
                            'Denim Suit',
                            'Dinner Jacket',
                            'Dinner Suit',
                            'Directors Cut Suit',
                            'Double Breasted Suit',
                            'Double Rider Suit',
                            'Double Vent Suit',
                            'Drape Cut Suit',
                            'Dress Suit',
                            'Electric Suit',
                            'Embossed Suit',
                            'Emperor Suit',
                            'Evening Suit',
                            'Executive Suit',
                            'Fashion Suit',
                            'Fitted Suit',
                            'Flannel Suit',
                            'Formal Suit',
                            'French Cuff Suit',
                            'Glen Check Suit',
                            'Glen Plaid Suit',
                            'Golf Suit',
                            'Gurkha Trousers Suit',
                            'Harris Tweed Suit',
                            'Herringbone Suit',
                            'Hiking Suit',
                            'Holiday Suit',
                            'Hombre Suit',
                            'Houndstooth Suit',
                            'Hunting Suit',
                            'Italian Suit',
                            'Jacquard Suit',
                            'Jodhpuri Suit',
                            'Kashmir Suit',
                            'Khadi Suit',
                            'Khaki Suit',
                            'Kimono Suit',
                            'Kissing Buttons Suit',
                            'Knit Suit',
                            'Leather Suit',
                            'Leisure Suit',
                            'Linen Suit',
                            'Lounge Suit',
                            'Made-to-Measure Suit',
                            'Mandarin Collar Suit',
                            'Mariner Suit',
                            'Matador Suit',
                            'Medal Ribbon Suit',
                            'Military Suit',
                            'Modern Fit Suit',
                            'Mohair Suit',
                            'Morning Coat',
                            'Morning Suit',
                            'Mourning Suit',
                            'Navy Blazer Suit',
                            'Nehru Suit',
                            'Night Suit',
                            'No Vent Suit',
                            'Norfolk Jacket Suit',
                            'Notch Lapel Suit',
                            'Official Suit',
                            'Off-the-Rack Suit',
                            'One Button Suit',
                            'Opera Suit',
                            'Overcoat Suit',
                            'Oxford Suit',
                            'Pacific Blazer Suit',
                            'Paisley Suit',
                            'Palm Beach Suit',
                            'Panama Suit',
                            'Pant Suit',
                            'Patch Pocket Suit',
                            'Peak Lapel Suit',
                            'Pea Coat Suit',
                            'Pencil Stripe Suit',
                            'Pinstripe Suit',
                            'Plaid Suit',
                            'Polo Suit',
                            'Polyester Suit',
                            'Polo Coat Suit',
                            'Prince Coat',
                            'Prince of Wales Suit',
                            'Quilted Suit',
                            'Racing Suit',
                            'Rajput Suit',
                            'Ready-to-Wear Suit',
                            'Relaxed Fit Suit',
                            'Riding Suit',
                            'Robe Suit',
                            'Rough Suit',
                            'Safari Suit',
                            'Sailor Suit',
                            'Satin Suit',
                            'School Blazer Suit',
                            'Seersucker Suit',
                            'Separates Suit',
                            'Shawl Collar Suit',
                            'Shawl Lapel Tuxedo',
                            'Shell Suit',
                            'Shirt Jacket Suit',
                            'Side Vent Suit',
                            'Silk Suit',
                            'Single Breasted Suit',
                            'Single Vent Suit',
                            'Skirt Suit',
                            'Slim Fit Suit',
                            'Smoking Jacket Suit',
                            'Space Suit',
                            'Spanish Suit',
                            'Sport Coat Suit',
                            'Sports Suit',
                            'Square Suit',
                            'Stroller Suit',
                            'Summer Suit',
                            'Super 100s Suit',
                            'Super 150s Suit',
                            'Surcoat Suit',
                            'Suspenders Suit',
                            'Tailcoat',
                            'Tailored Suit',
                            'Tartan Suit',
                            'Tennis Blazer Suit',
                            'Three Button Roll Suit',
                            'Three Piece Suit',
                            'Tracksuit',
                            'Trapeze Suit',
                            'Travel Suit',
                            'Trench Coat Suit',
                            'Trousers Suit',
                            'T-Shirt Suit',
                            'Tunic Suit',
                            'Tuxedo',
                            'Tweed Suit',
                            'Two Button Suit',
                            'Two Piece Suit',
                            'Uniform Suit',
                            'Unstructured Suit',
                            'Utility Suit',
                            'Valet Suit',
                            'Varsity Suit',
                            'Velvet Suit',
                            'Ventless Suit',
                            'Vest Suit',
                            'Vintage Suit',
                            'Walking Suit',
                            'Wardrobe Suit',
                            'Wash-and-Wear Suit',
                            'Wedding Suit',
                            'Weekender Suit',
                            'Western Suit',
                            'Whipcord Suit',
                            'White Tie Suit',
                            'Windsor Suit',
                            'Wing Collar Suit',
                            'Winter Suit',
                            'Wool Suit',
                            'Woolen Suit',
                            'Work Suit',
                            'Yachting Suit',
                            'Zoot Suit',
                            'Other',
                        ];

                        // Determine user gender and set appropriate subtypes
                        $userGender = auth()->check() ? auth()->user()->gender : null;

                        if ($userGender === 'Female') {
                            $subtypes = $gownTypes;
                        } elseif ($userGender === 'Male') {
                            $subtypes = $suitTypes;
                        } else {
                            // For guests or users with other/prefer not to say gender, show all
                            $subtypes = array_merge($gownTypes, $suitTypes);
                            sort($subtypes); // Sort alphabetically when showing all
                        }

                        $selectedSubtypes = request('subtype', []);
                        if (!is_array($selectedSubtypes)) {
                            $selectedSubtypes = [$selectedSubtypes];
                        }
                    @endphp

                    @foreach($subtypes as $subtype)
                        <label
                            class="subtype-item flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" name="subtype[]" value="{{ $subtype }}"
                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500 filter-input subtype-checkbox"
                                {{ in_array($subtype, $selectedSubtypes) ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-700 font-medium">{{ $subtype }}</span>
                        </label>
                    @endforeach
                </div>

                @if(count($selectedSubtypes) > 0)
                    <div class="mt-4 px-2">
                        <button type="button" onclick="clearSelectedSubtypes()"
                            class="w-full py-2 text-sm bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear Selected ({{ count($selectedSubtypes) }})
                        </button>
                    </div>
                @endif
            </details>
        </div>

        <!-- Size Filter -->
        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
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

                <!-- Search Box for Sizes -->
                <div class="mt-4 mb-3 px-2">
                    <div class="relative">
                        <input type="text" id="sizeSearch" placeholder="Search sizes..."
                            class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                            onkeyup="filterSizes()">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button type="button" onclick="clearSizeSearch()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 flex justify-between">
                        <span id="sizeVisibleCount">Showing all sizes</span>
                        <button type="button" onclick="selectAllSizes()"
                            class="text-purple-600 hover:text-purple-800 text-xs font-medium">
                            Select All
                        </button>
                    </div>
                </div>

                <div id="sizeList" class="mt-3 space-y-3 pl-2 max-h-96 overflow-y-auto pr-2">
                    @php
                        $sizeOptions = [
                            // Individual Sizes
                            'XS' => 'Extra Small (XS)',
                            'S' => 'Small (S)',
                            'M' => 'Medium (M)',
                            'L' => 'Large (L)',
                            'XL' => 'Extra Large (XL)',
                            'XXL' => '2X Large (XXL)',
                            'XXXL' => '3X Large (XXXL)',

                            // Common Ranges
                            'XS-S' => 'XS to S',
                            'S-M' => 'S to M',
                            'M-L' => 'M to L',
                            'L-XL' => 'L to XL',
                            'XL-XXL' => 'XL to XXL',

                            // Extended Ranges
                            'XXS-S' => 'XXS to S',
                            'XS-M' => 'XS to M',
                            'S-L' => 'S to L',
                            'M-XL' => 'M to XL',
                            'L-XXL' => 'L to XXL',
                            'XXS-M' => 'XXS to M',
                            'XS-L' => 'XS to L',
                            'S-XL' => 'S to XL',
                            'M-XXL' => 'M to XXL',

                            // Broad Ranges
                            'XXS-L' => 'XXS to L',
                            'XS-XL' => 'XS to XL',
                            'S-XXL' => 'S to XXL',
                            'XXS-XL' => 'XXS to XL',
                            'XS-XXL' => 'XS to XXL',

                            // Special
                            'Adjustable' => 'Adjustable/Customizable',

                        ];

                        $selectedSizes = request('size', []);
                        if (!is_array($selectedSizes)) {
                            $selectedSizes = [$selectedSizes];
                        }
                    @endphp

                    @foreach($sizeOptions as $sizeValue => $sizeLabel)
                        <label
                            class="size-item flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" name="size[]" value="{{ $sizeValue }}"
                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500 filter-input size-checkbox"
                                {{ in_array($sizeValue, $selectedSizes) ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-700 font-medium">{{ $sizeLabel }}</span>
                        </label>
                    @endforeach
                </div>

                @if(count($selectedSizes) > 0)
                    <div class="mt-4 px-2">
                        <button type="button" onclick="clearSelectedSizes()"
                            class="w-full py-2 text-sm bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear Selected ({{ count($selectedSizes) }})
                        </button>
                    </div>
                @endif
            </details>
        </div>

        <!-- Color Filter -->
        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
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
                <div class="mt-3 grid grid-cols-5 gap-3">
                    @php
                        $colors = [
                            // Basic Colors
                            'Black' => 'bg-black',
                            'White' => 'bg-white ring-1 ring-gray-300',
                            'Gray' => 'bg-gray-500',
                            'Silver' => 'bg-gray-400',
                            'Brown' => 'bg-amber-800',

                            // Primary Colors
                            'Red' => 'bg-red-600',
                            'Blue' => 'bg-blue-600',
                            'Green' => 'bg-green-600',
                            'Yellow' => 'bg-yellow-500',
                            'Orange' => 'bg-orange-500',

                            // Secondary Colors
                            'Purple' => 'bg-purple-600',
                            'Pink' => 'bg-pink-500',
                            'Teal' => 'bg-teal-500',
                            'Indigo' => 'bg-indigo-600',
                            'Lime' => 'bg-lime-500',
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

        <!-- Event Filter -->
        <div class="mb-6 border-b border-gray-200 pb-4">
            <details class="group">
                <summary
                    class="flex justify-between items-center cursor-pointer py-3 text-lg font-semibold text-gray-800 hover:text-purple-700 transition-colors duration-200 bg-gray-50 rounded-lg px-4">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                        Event
                    </span>
                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </span>
                </summary>

                <!-- Search Box for Events -->
                <div class="mt-4 mb-3 px-2">
                    <div class="relative">
                        <input type="text" id="eventSearch" placeholder="Search events..."
                            class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                            onkeyup="filterEvents()">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button type="button" onclick="clearEventSearch()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 flex justify-between">
                        <span id="eventVisibleCount">Showing all events</span>
                        <button type="button" onclick="selectAllEvents()"
                            class="text-purple-600 hover:text-purple-800 text-xs font-medium">
                            Select All
                        </button>
                    </div>
                </div>

                <div id="eventList" class="mt-3 space-y-3 pl-2 max-h-96 overflow-y-auto pr-2">
                    @php
                        $events = [
                            // Weddings & Related
                            'Wedding',
                            'Engagement Party',
                            'Bridal Shower',
                            'Rehearsal Dinner',

                            // Formal & Red Carpet
                            'Gala',
                            'Black Tie Event',
                            'Awards Night',
                            'Charity Ball',
                            'Red Carpet Event',

                            // School / Formal Youth Events
                            'Prom',
                            'Graduation',
                            'Homecoming',
                            'Formal Dance',

                            // Cultural & Ceremonial
                            'Debut / 18th Birthday',
                            'Quinceañera',
                            'Pageant',

                            // Professional / Business
                            'Corporate Event',
                            'Business Gala',

                            // Holiday & Seasonal
                            'Christmas Party',
                            "New Year's Eve",
                            'Holiday Ball',

                            // Special Shoots / Exhibitions
                            'Photo Shoot',
                            'Fashion Show',

                            // Family Occasions
                            'Anniversary',
                            'Birthday Celebration',

                            // Other
                            'Other',
                        ];

                        $selectedEvents = request('event', []);
                        if (!is_array($selectedEvents)) {
                            $selectedEvents = [$selectedEvents];
                        }
                    @endphp

                    @foreach($events as $event)
                        <label
                            class="event-item flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors duration-200 cursor-pointer">
                            <input type="checkbox" name="event[]" value="{{ $event }}"
                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500 filter-input event-checkbox"
                                {{ in_array($event, $selectedEvents) ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-700 font-medium">{{ $event }}</span>
                        </label>
                    @endforeach
                </div>

                @if(count($selectedEvents) > 0)
                    <div class="mt-4 px-2">
                        <button type="button" onclick="clearSelectedEvents()"
                            class="w-full py-2 text-sm bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear Selected ({{ count($selectedEvents) }})
                        </button>
                    </div>
                @endif
            </details>
        </div>

        <!-- Clear Filters Button Only -->
        <div class="flex">
            <button type="button" id="clearFiltersBtn"
                class="w-full py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                Clear All Filters
            </button>
        </div>
    </form>

    <div class="flex flex-col flex-grow mt-8 lg:mt-0">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
            <h1 class="text-4xl font-bold text-gray-800 text-center sm:text-left font-serif">Browse Collections</h1>
            <p class="text-gray-600 mt-2 text-center sm:text-left">Discover the perfect outfit for every occasion</p>
        </div>

        <!-- Products Grid with Skeleton Loading -->
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

    .toggle-checkbox:checked+.toggle-label {
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Custom scrollbar for filter lists */
    #subtypeList::-webkit-scrollbar,
    #sizeList::-webkit-scrollbar,
    #eventList::-webkit-scrollbar {
        width: 6px;
    }

    #subtypeList::-webkit-scrollbar-track,
    #sizeList::-webkit-scrollbar-track,
    #eventList::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #subtypeList::-webkit-scrollbar-thumb,
    #sizeList::-webkit-scrollbar-thumb,
    #eventList::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 10px;
    }

    #subtypeList::-webkit-scrollbar-thumb:hover,
    #sizeList::-webkit-scrollbar-thumb:hover,
    #eventList::-webkit-scrollbar-thumb:hover {
        background: #8b5cf6;
    }

    .subtype-item,
    .size-item,
    .event-item {
        transition: all 0.2s ease;
    }

    .subtype-item:hover,
    .size-item:hover,
    .event-item:hover {
        background-color: #f5f3ff;
    }

    .fade-out {
        opacity: 0;
        transition: opacity 0.3s ease-out;
    }

    .fade-in {
        opacity: 1;
        transition: opacity 0.3s ease-in;
    }

    /* Search input styling */
    input[type="text"] {
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    input[type="text"]:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    /* Hide checkboxes visually but keep accessible */
    .filter-input {
        position: relative;
        cursor: pointer;
    }

    .filter-input:checked+span {
        font-weight: 600;
        color: #7c3aed;
    }
</style>

<script>
    // Subtype filter search functionality
    function filterSubtypes() {
        const searchTerm = document.getElementById('subtypeSearch').value.toLowerCase();
        const subtypeItems = document.querySelectorAll('.subtype-item');
        let visibleCount = 0;

        subtypeItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Update counter
        const totalCount = subtypeItems.length;
        const counterElement = document.getElementById('subtypeVisibleCount');
        if (searchTerm === '') {
            counterElement.textContent = `Showing all ${totalCount} styles`;
        } else {
            counterElement.textContent = `Showing ${visibleCount} of ${totalCount} styles`;
        }
    }

    function clearSubtypeSearch() {
        document.getElementById('subtypeSearch').value = '';
        filterSubtypes();
    }

    function selectAllSubtypes() {
        const checkboxes = document.querySelectorAll('.subtype-checkbox');
        const searchTerm = document.getElementById('subtypeSearch').value.toLowerCase();

        if (searchTerm === '') {
            // If no search term, select all visible
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('.subtype-item').style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
        } else {
            // If there's a search term, select only visible ones
            checkboxes.forEach(checkbox => {
                const item = checkbox.closest('.subtype-item');
                checkbox.checked = item.style.display !== 'none';
            });
        }

        // Trigger change event to update filters
        checkboxes.forEach(checkbox => {
            checkbox.dispatchEvent(new Event('change'));
        });
    }

    function clearSelectedSubtypes() {
        const checkboxes = document.querySelectorAll('.subtype-checkbox:checked');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        });
    }

    // Size filter search functionality
    function filterSizes() {
        const searchTerm = document.getElementById('sizeSearch').value.toLowerCase();
        const sizeItems = document.querySelectorAll('.size-item');
        let visibleCount = 0;

        sizeItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Update counter
        const totalCount = sizeItems.length;
        const counterElement = document.getElementById('sizeVisibleCount');
        if (searchTerm === '') {
            counterElement.textContent = `Showing all ${totalCount} sizes`;
        } else {
            counterElement.textContent = `Showing ${visibleCount} of ${totalCount} sizes`;
        }
    }

    function clearSizeSearch() {
        document.getElementById('sizeSearch').value = '';
        filterSizes();
    }

    function selectAllSizes() {
        const checkboxes = document.querySelectorAll('.size-checkbox');
        const searchTerm = document.getElementById('sizeSearch').value.toLowerCase();

        if (searchTerm === '') {
            // If no search term, select all visible
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('.size-item').style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
        } else {
            // If there's a search term, select only visible ones
            checkboxes.forEach(checkbox => {
                const item = checkbox.closest('.size-item');
                checkbox.checked = item.style.display !== 'none';
            });
        }

        // Trigger change event to update filters
        checkboxes.forEach(checkbox => {
            checkbox.dispatchEvent(new Event('change'));
        });
    }

    function clearSelectedSizes() {
        const checkboxes = document.querySelectorAll('.size-checkbox:checked');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        });
    }

    // Event filter search functionality
    function filterEvents() {
        const searchTerm = document.getElementById('eventSearch').value.toLowerCase();
        const eventItems = document.querySelectorAll('.event-item');
        let visibleCount = 0;

        eventItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Update counter
        const totalCount = eventItems.length;
        const counterElement = document.getElementById('eventVisibleCount');
        if (searchTerm === '') {
            counterElement.textContent = `Showing all ${totalCount} events`;
        } else {
            counterElement.textContent = `Showing ${visibleCount} of ${totalCount} events`;
        }
    }

    function clearEventSearch() {
        document.getElementById('eventSearch').value = '';
        filterEvents();
    }

    function selectAllEvents() {
        const checkboxes = document.querySelectorAll('.event-checkbox');
        const searchTerm = document.getElementById('eventSearch').value.toLowerCase();

        if (searchTerm === '') {
            // If no search term, select all visible
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('.event-item').style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
        } else {
            // If there's a search term, select only visible ones
            checkboxes.forEach(checkbox => {
                const item = checkbox.closest('.event-item');
                checkbox.checked = item.style.display !== 'none';
            });
        }

        // Trigger change event to update filters
        checkboxes.forEach(checkbox => {
            checkbox.dispatchEvent(new Event('change'));
        });
    }

    function clearSelectedEvents() {
        const checkboxes = document.querySelectorAll('.event-checkbox:checked');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        });
    }

    // Add event listeners for search functionality
    document.addEventListener('DOMContentLoaded', function () {
        // Subtype search
        const subtypeSearch = document.getElementById('subtypeSearch');
        if (subtypeSearch) {
            subtypeSearch.addEventListener('input', filterSubtypes);
            subtypeSearch.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    clearSubtypeSearch();
                }
            });
        }

        // Size search
        const sizeSearch = document.getElementById('sizeSearch');
        if (sizeSearch) {
            sizeSearch.addEventListener('input', filterSizes);
            sizeSearch.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    clearSizeSearch();
                }
            });
        }

        // Event search
        const eventSearch = document.getElementById('eventSearch');
        if (eventSearch) {
            eventSearch.addEventListener('input', filterEvents);
            eventSearch.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    clearEventSearch();
                }
            });
        }

        // Initialize counters
        const totalSubtypes = document.querySelectorAll('.subtype-item').length;
        const totalSizes = document.querySelectorAll('.size-item').length;
        const totalEvents = document.querySelectorAll('.event-item').length;

        if (document.getElementById('subtypeVisibleCount')) {
            document.getElementById('subtypeVisibleCount').textContent = `Showing all ${totalSubtypes} styles`;
        }

        if (document.getElementById('sizeVisibleCount')) {
            document.getElementById('sizeVisibleCount').textContent = `Showing all ${totalSizes} sizes`;
        }

        if (document.getElementById('eventVisibleCount')) {
            document.getElementById('eventVisibleCount').textContent = `Showing all ${totalEvents} events`;
        }

        // Filter form AJAX functionality
        const filterForm = document.getElementById('filterForm');
        const productsContainer = document.getElementById('productsContainer');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        const filterInputs = document.querySelectorAll('.filter-input');

        let isLoading = false;
        let currentRequest = null;
        let debounceTimer;

        // Show skeleton loading
        function showSkeletonLoading() {
            const skeletonHTML = `
            <div id="skeleton-loading">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
                    ${Array(8).fill(0).map(() => `
                        <div class="max-w-[400px] w-full animate-pulse">
                            <div class="h-96 sm:h-[400px] md:h-[400px] lg:h-[400px] xl:h-[400px] w-full bg-gray-300 rounded-md shadow-md"></div>
                            <div class="mt-4 space-y-2">
                                <div class="h-5 bg-gray-300 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

            productsContainer.innerHTML = skeletonHTML;
        }

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
                if (key.endsWith('[]')) {
                    if (!data[key]) {
                        data[key] = [];
                    }
                    data[key].push(value);
                } else {
                    data[key] = value;
                }
            }

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

            // Show skeleton loading
            showSkeletonLoading();

            // Build URL with parameters
            const params = new URLSearchParams();
            Object.keys(formData).forEach(key => {
                if (Array.isArray(formData[key])) {
                    formData[key].forEach(value => {
                        params.append(key, value);
                    });
                } else {
                    params.append(key, formData[key]);
                }
            });

            // AJAX request
            currentRequest = new XMLHttpRequest();
            currentRequest.open('GET', '{{ route("product.list") }}?' + params.toString());
            currentRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            currentRequest.onload = function () {
                if (currentRequest.status === 200) {
                    productsContainer.innerHTML = currentRequest.responseText;

                    // Re-initialize pagination event listeners
                    initializePaginationListeners();
                } else {
                    console.error('Error loading products');
                    productsContainer.innerHTML = '<div class="col-span-full text-center text-red-500 py-8">Error loading products. Please try again.</div>';
                }

                isLoading = false;
                currentRequest = null;
            };

            currentRequest.onerror = function () {
                console.error('Request failed');
                isLoading = false;
                currentRequest = null;
            };

            currentRequest.send();
        }

        // Initialize pagination event listeners
        function initializePaginationListeners() {
            const paginationLinks = document.querySelectorAll('.pagination-link');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    if (url) {
                        loadPage(url);
                    }
                });
            });
        }

        // Load page via AJAX
        function loadPage(url) {
            if (isLoading) {
                if (currentRequest) {
                    currentRequest.abort();
                }
            }

            isLoading = true;

            // Show skeleton loading
            showSkeletonLoading();

            // AJAX request
            currentRequest = new XMLHttpRequest();
            currentRequest.open('GET', url);
            currentRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            currentRequest.onload = function () {
                if (currentRequest.status === 200) {
                    productsContainer.innerHTML = currentRequest.responseText;

                    // Update URL without page reload
                    if (history.pushState) {
                        const newUrl = new URL(url, window.location.origin);
                        window.history.pushState({ path: newUrl.href }, '', newUrl.href);
                    }

                    // Re-initialize pagination event listeners
                    initializePaginationListeners();

                    // Scroll to top of products section
                    productsContainer.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                } else {
                    console.error('Error loading page');
                    window.location.href = url;
                }

                isLoading = false;
                currentRequest = null;
            };

            currentRequest.onerror = function () {
                console.error('Request failed');
                window.location.href = url;
                isLoading = false;
                currentRequest = null;
            };

            currentRequest.send();
        }

        // Debounce function to prevent too many requests
        function debounceLoadProducts() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(loadProducts, 500);
        }

        // Add event listeners to filter inputs
        filterInputs.forEach(input => {
            input.addEventListener('change', debounceLoadProducts);
        });

        // Clear filters button
        clearFiltersBtn.addEventListener('click', function () {
            // Reset form
            filterForm.reset();

            // Clear search boxes
            if (subtypeSearch) subtypeSearch.value = '';
            if (sizeSearch) sizeSearch.value = '';
            if (eventSearch) eventSearch.value = '';

            // Update filter displays
            filterSubtypes();
            filterSizes();
            filterEvents();

            // Clear URL parameters
            window.history.replaceState({}, '', window.location.pathname);

            // Reload products
            loadProducts();
        });

        // Initialize pagination listeners on page load
        initializePaginationListeners();

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function (e) {
            loadProducts();
        });
    });
</script>