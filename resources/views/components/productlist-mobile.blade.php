@props(['products'])

<div class="mobile-app-products min-h-screen bg-gray-50">
    {{-- Mobile Header --}}
    <div class="sticky top-0 z-40 bg-white border-b border-gray-200 px-4 py-3 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <button onclick="window.history.back()" class="p-2 rounded-full hover:bg-gray-100 active:bg-gray-200">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <h1 class="text-lg font-semibold text-gray-900 playfair-display">Collections</h1>

            {{-- Filter Button --}}
            <button id="filterButton" class="p-2 rounded-full hover:bg-gray-100 active:bg-gray-200">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
            </button>
        </div>

        </div>

    {{-- Filter Sidebar (Hidden by default) --}}
    <div id="filterSidebar" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden">
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-sm bg-white shadow-xl overflow-y-auto">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Filters</h2>
                    <button id="closeFilter" class="p-2 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                {{-- Filters Form --}}
                <form id="mobileFilterForm" method="GET" action="{{ route('product.list') }}">
                    <input type="hidden" name="app" value="1">
                    <input type="hidden" name="mobile_nav" value="true">
                    
                    {{-- Type Filter (only for guests) --}}
                    @guest
                        <div class="mb-4">
                            <details class="group border-b border-gray-200 pb-3">
                                <summary class="flex justify-between items-center cursor-pointer py-3 text-gray-800 hover:text-purple-700 transition-colors">
                                    <span class="flex items-center font-semibold">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Type
                                    </span>
                                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </summary>
                                <div class="mt-3 space-y-2 pl-2">
                                    @php
                                        $types = ['Gown', 'Suit'];
                                        $selectedTypes = request('type', []);
                                        if (!is_array($selectedTypes)) {
                                            $selectedTypes = [$selectedTypes];
                                        }
                                    @endphp
                                    @foreach($types as $type)
                                        <label class="flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors cursor-pointer">
                                            <input type="checkbox" name="type[]" value="{{ $type }}"
                                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                                {{ in_array($type, $selectedTypes) ? 'checked' : '' }}>
                                            <span class="ml-3 text-gray-700 text-sm">{{ $type }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    @endguest

                    {{-- Style Filter --}}
                    <div class="mb-4">
                        <details class="group border-b border-gray-200 pb-3">
                            <summary class="flex justify-between items-center cursor-pointer py-3 text-gray-800 hover:text-purple-700 transition-colors">
                                <span class="flex items-center font-semibold">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Style
                                </span>
                                <span class="transform transition-transform duration-200 group-open:rotate-180">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </summary>
                            <div class="mt-3 max-h-60 overflow-y-auto space-y-2 pl-2 pr-2">
                                @php
                                    // Determine user gender and set appropriate subtypes
                                    $userGender = auth()->check() ? auth()->user()->gender : null;
                                    $gownTypes = ['A-line Gown',
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
                            'Other',];
                                    $suitTypes = ['2-Button Suit',
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
                            'Other',];

                                    if ($userGender === 'Female') {
                                        $subtypes = $gownTypes;
                                    } elseif ($userGender === 'Male') {
                                        $subtypes = $suitTypes;
                                    } else {
                                        $subtypes = array_merge($gownTypes, $suitTypes);
                                        sort($subtypes);
                                    }

                                    $selectedSubtypes = request('subtype', []);
                                    if (!is_array($selectedSubtypes)) {
                                        $selectedSubtypes = [$selectedSubtypes];
                                    }
                                @endphp
                                @foreach($subtypes as $subtype)
                                    <label class="flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors cursor-pointer">
                                        <input type="checkbox" name="subtype[]" value="{{ $subtype }}"
                                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                            {{ in_array($subtype, $selectedSubtypes) ? 'checked' : '' }}>
                                        <span class="ml-3 text-gray-700 text-sm">{{ $subtype }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </details>
                    </div>

                    {{-- Size Filter --}}
                    <div class="mb-4">
                        <details class="group border-b border-gray-200 pb-3">
                            <summary class="flex justify-between items-center cursor-pointer py-3 text-gray-800 hover:text-purple-700 transition-colors">
                                <span class="flex items-center font-semibold">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                    </svg>
                                    Size
                                </span>
                                <span class="transform transition-transform duration-200 group-open:rotate-180">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </summary>
                            <div class="mt-3 max-h-60 overflow-y-auto space-y-2 pl-2 pr-2">
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
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($sizeOptions as $sizeValue => $sizeLabel)
                                        <label class="flex flex-col items-center p-2 rounded-lg hover:bg-purple-50 transition-colors cursor-pointer">
                                            <input type="checkbox" name="size[]" value="{{ $sizeValue }}"
                                                class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                                {{ in_array($sizeValue, $selectedSizes) ? 'checked' : '' }}>
                                            <span class="mt-1 text-gray-700 text-xs text-center">{{ $sizeLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </details>
                    </div>

                    {{-- Color Filter --}}
                    <div class="mb-4">
                        <details class="group border-b border-gray-200 pb-3">
                            <summary class="flex justify-between items-center cursor-pointer py-3 text-gray-800 hover:text-purple-700 transition-colors">
                                <span class="flex items-center font-semibold">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                    </svg>
                                    Color
                                </span>
                                <span class="transform transition-transform duration-200 group-open:rotate-180">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </summary>
                            <div class="mt-3 pl-2">
                                <div class="grid grid-cols-5 gap-3">
                                    @php
                                        $colors = [
                                            'Black' => 'bg-black',
                                            'White' => 'bg-white ring-1 ring-gray-300',
                                            'Gray' => 'bg-gray-500',
                                            'Silver' => 'bg-gray-400',
                                            'Brown' => 'bg-amber-800',
                                            'Red' => 'bg-red-600',
                                            'Blue' => 'bg-blue-600',
                                            'Green' => 'bg-green-600',
                                            'Yellow' => 'bg-yellow-500',
                                            'Orange' => 'bg-orange-500',
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
                                            <input type="checkbox" name="color[]" value="{{ $colorName }}" class="hidden peer"
                                                {{ in_array($colorName, $selectedColors) ? 'checked' : '' }}>
                                            <span
                                                class="w-8 h-8 rounded-full border-2 border-transparent cursor-pointer shadow-sm
                                                {{ $colorClass }} peer-checked:border-purple-600 peer-checked:ring-2 peer-checked:ring-purple-200 transition-all transform peer-checked:scale-110 hover:scale-105"
                                                title="{{ $colorName }}"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </details>
                    </div>

                    {{-- Event Filter --}}
                    <div class="mb-4">
                        <details class="group border-b border-gray-200 pb-3">
                            <summary class="flex justify-between items-center cursor-pointer py-3 text-gray-800 hover:text-purple-700 transition-colors">
                                <span class="flex items-center font-semibold">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                    Event
                                </span>
                                <span class="transform transition-transform duration-200 group-open:rotate-180">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </summary>
                            <div class="mt-3 max-h-60 overflow-y-auto space-y-2 pl-2 pr-2">
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
                                    <label class="flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors cursor-pointer">
                                        <input type="checkbox" name="event[]" value="{{ $event }}"
                                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                            {{ in_array($event, $selectedEvents) ? 'checked' : '' }}>
                                        <span class="ml-3 text-gray-700 text-sm">{{ $event }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </details>
                    </div>

                    {{-- Measurements Filter (for logged in users) --}}
                    @auth
                        <div class="mb-4">
                            <details class="group border-b border-gray-200 pb-3">
                                <summary class="flex justify-between items-center cursor-pointer py-3 text-gray-800 hover:text-purple-700 transition-colors">
                                    <span class="flex items-center font-semibold">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        My Measurements
                                    </span>
                                    <span class="transform transition-transform duration-200 group-open:rotate-180">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </summary>
                                <div class="mt-3 pl-2">
                                    <label class="flex items-center p-2 rounded-lg hover:bg-purple-50 transition-colors cursor-pointer">
                                        <input type="checkbox" name="measurements_filter" value="1"
                                            class="form-checkbox text-purple-600 rounded focus:ring-purple-500"
                                            {{ request('measurements_filter') ? 'checked' : '' }}>
                                        <span class="ml-3 text-gray-700 text-sm">Show products that fit my measurements</span>
                                    </label>
                                </div>
                            </details>
                        </div>
                    @endauth

                    {{-- Action Buttons --}}
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 mt-4">
                        <div class="flex space-x-3">
                            <button type="button" onclick="clearFilters()"
                                class="flex-1 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 active:scale-95 transition-all">
                                Clear All
                            </button>
                            <button type="submit"
                                class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 active:scale-95 transition-all">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Products Grid --}}
    <div class="p-4">
        @if($products->count() > 0)
            <div class="grid grid-cols-2 gap-3">
                @foreach($products as $product)
                    <a href="{{ route('product.overview', ['product_id' => $product->product_id]) }}?app=1&mobile_nav=true"
                        class="block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden active:scale-[0.98] transition-all duration-200">

                        {{-- Product Image --}}
                        <div class="relative h-48 bg-gray-100">
                            @php
                                $imageRecord = $product->product_images->first();
                            @endphp

                            @if($imageRecord && $imageRecord->thumbnail_image)
                                <img src="{{ asset('uploads/' . $imageRecord->thumbnail_image) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Recommendation Badge --}}
                            @if(auth()->check() && $product->fit_score > 0)
                                                <div class="absolute top-2 left-2">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                        {{ $product->recommendation_level === 'Perfect Fit' ? 'bg-green-100 text-green-800' :
                                ($product->recommendation_level === 'Great Fit' ? 'bg-blue-100 text-blue-800' :
                                    'bg-yellow-100 text-yellow-800') }}">
                                                        {{ $product->fit_score }}%
                                                    </span>
                                                </div>
                            @endif

                            {{-- 3D Model Indicator --}}
                            @if($product->product_3d_models && $product->product_3d_models->count() > 0)
                                <div class="absolute top-2 right-2 bg-purple-600 text-white p-1.5 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Product Info --}}
                        <div class="p-3">
                            <h3 class="font-semibold text-gray-900 text-sm line-clamp-1 mb-1">
                                {{ $product->name }}
                            </h3>
                            <p class="text-xs text-gray-600 mb-2">{{ $product->subtype }}</p>

                            @if(!$product->has_actual_measurements)
                                <span class="inline-flex items-center px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    No measurements
                                </span>
                            @endif

                            <p class="text-xs text-gray-500 mt-2">{{ $product->user->shop->shop_name }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- No Pagination - All products shown --}}
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">
                    Showing {{ $products->count() }} product{{ $products->count() !== 1 ? 's' : '' }}
                </p>
            </div>

        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 px-4">
                <div
                    class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600 text-center mb-6 max-w-sm">
                    Try adjusting your filters
                </p>
                <a href="{{ route('product.list') }}?app=1&mobile_nav=true"
                    class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 active:scale-95">
                    Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Filter sidebar functionality
        const filterButton = document.getElementById('filterButton');
        const filterSidebar = document.getElementById('filterSidebar');
        const closeFilter = document.getElementById('closeFilter');
        const mobileFilterForm = document.getElementById('mobileFilterForm');

        if (filterButton && filterSidebar) {
            filterButton.addEventListener('click', () => {
                filterSidebar.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });

            closeFilter.addEventListener('click', () => {
                filterSidebar.classList.add('hidden');
                document.body.style.overflow = '';
            });

            // Close sidebar when clicking outside
            filterSidebar.addEventListener('click', (e) => {
                if (e.target === filterSidebar) {
                    filterSidebar.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        }

        // Clear all filters
        window.clearFilters = function () {
            window.location.href = '{{ route("product.list") }}?app=1&mobile_nav=true';
        };

        // Handle pull-to-refresh
        let touchStartY = 0;
        document.addEventListener('touchstart', function (e) {
            if (window.scrollY === 0) {
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        document.addEventListener('touchmove', function (e) {
            if (touchStartY === 0) return;

            const touchY = e.touches[0].clientY;
            const diff = touchY - touchStartY;

            // Pull down to refresh (100px threshold)
            if (window.scrollY === 0 && diff > 100) {
                location.reload();
            }
        }, { passive: true });

        // Close dropdowns when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const detailsElements = document.querySelectorAll('details[open]');
            detailsElements.forEach(details => {
                if (!details.contains(e.target)) {
                    details.removeAttribute('open');
                }
            });
        });
    });
</script>

<style>
    .mobile-app-products {
        -webkit-overflow-scrolling: touch;
        padding-top: env(safe-area-inset-top);
    }

    /* iOS-style active states */
    .mobile-app-products a:active {
        transform: scale(0.98);
    }

    /* Smooth transitions for filter sidebar */
    #filterSidebar > div {
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
        }
        to {
            transform: translateX(0);
        }
    }

    /* Custom scrollbar for dropdowns */
    details div[class*="max-h-60"]::-webkit-scrollbar {
        width: 4px;
    }

    details div[class*="max-h-60"]::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }

    details div[class*="max-h-60"]::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 2px;
    }

    /* Dropdown animation */
    details[open] summary ~ * {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>