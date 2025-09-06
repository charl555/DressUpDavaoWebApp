<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 px-4 sm:px-6 lg:px-8 pt-[200px] pb-12 max-w-7xl mx-auto">
    {{-- {# Main container for the product overview. #}
    {# - Adjusted pt- to 150px for slightly less top spacing. #}
    {# - Added pb-12 for bottom padding. #}
    {# - Increased overall horizontal padding for better content breathing room on different screen sizes. #}
    {# - Added max-w-7xl and mx-auto to center the entire component on very large screens and control its width. #}
    {# - Increased gap for better spacing between columns on larger screens. #} --}}

    <div class="flex flex-col">
        {{-- {# Group for the 3D model and its thumbnails. #} --}}

        <div class="bg-gray-100 rounded-lg overflow-hidden shadow-lg aspect-video w-full h-[600px] mb-4">
            {{-- {# Increased height for the 3D model viewer and added responsive classes. #}
            {# - bg-gray-100: A neutral background if the model takes time to load. #}
            {# - rounded-lg overflow-hidden shadow-lg: Consistent styling. #}
            {# - aspect-video: Ensures a 16:9 ratio for the viewer, making it responsive. #}
            {# - w-full: Takes full width. #}
            {# - h-[600px]: Sets a fixed height, overriding aspect-video if needed for specific designs. Adjust this as
            necessary. #}
            {# - mb-4: Margin bottom for spacing from thumbnails. #} --}}
            <model-viewer alt="3D Model"
                src="{{ asset('storage/product-models/170cm-63kg-c96.2-w81.5-h95.3-i77.9.glb') }}" auto-rotate
                camera-controls ar shadow-intensity="1" class="w-full h-full">
                {{-- {# Ensured model-viewer fills its parent div. #} --}}
            </model-viewer>
        </div>

        <div class="grid grid-cols-4 gap-2">
            {{-- {# Changed to a grid for uniform thumbnail sizing and spacing. #}
            {# - gap-2: Small gap between thumbnails. #} --}}
            <div
                class="bg-gray-100 h-24 sm:h-32 rounded-md overflow-hidden cursor-pointer hover:ring-2 hover:ring-purple-500 transition-all duration-200 group">
                <img src="{{ asset('frontend-images/dress1.png') }}" alt="Product thumbnail 1"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            </div>
            <div
                class="bg-gray-100 h-24 sm:h-32 rounded-md overflow-hidden cursor-pointer hover:ring-2 hover:ring-purple-500 transition-all duration-200 group">
                <img src="{{ asset('frontend-images/dress2.png') }}" alt="Product thumbnail 2"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            </div>
            <div
                class="bg-gray-100 h-24 sm:h-32 rounded-md overflow-hidden cursor-pointer hover:ring-2 hover:ring-purple-500 transition-all duration-200 group">
                <img src="{{ asset('frontend-images/dress3.png') }}" alt="Product thumbnail 3"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            </div>
            <div
                class="bg-gray-100 h-24 sm:h-32 rounded-md overflow-hidden cursor-pointer hover:ring-2 hover:ring-purple-500 transition-all duration-200 group">
                <img src="{{ asset('frontend-images/dress4.png') }}" alt="Product thumbnail 4"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            </div>
            {{-- {# Replaced generic 'product image' divs with actual image placeholders and hover effects. #} --}}
        </div>
    </div>

    <div class="flex flex-col pt-8 lg:pt-0">
        {{-- {# Added pt-8 for spacing on small screens when columns stack, removed on large screens. #} --}}

        <div class="pb-6 border-b border-gray-200 mb-6">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900"
                style="font-family: 'Playfair Display', serif;">
                Red Dress
            </h1>
        </div>

        <div class="space-y-4 mb-8">
            {{-- {# Uses space-y for consistent vertical spacing between paragraphs. #} --}}
            <p class="text-gray-700 leading-relaxed">
                This is a red dress suitable for prom, debut, or formal events.
            </p>
            <p class="text-gray-700">
                <strong class="font-semibold">Inclusions:</strong> Gown, Garment Bag, Hanger
            </p>
            <p class="text-gray-700">
                <strong class="font-semibold">Type:</strong> Ball Gown
            </p>
            <p class="text-gray-700">
                <strong class="font-semibold">Subtype:</strong> Evening Wear
            </p>
            <p class="text-gray-700">
                <strong class="font-semibold">Occasions:</strong> Weddings, Prom, Gala
            </p>
            <p class="text-gray-700">
                <strong class="font-semibold">Color:</strong> White
            </p>
            <p class="text-gray-700">
                <strong class="font-semibold">Size:</strong> M (Medium)
            </p>
            <p class="text-gray-700">
                <strong class="font-semibold">Measurements (inches):</strong> Bust: 36, Waist: 28, Hips: 38
            </p>
            {{-- {# Consolidated info into strong tags for better scanning and clarity. #} --}}
        </div>

        <div class="flex justify-center md:justify-start mb-8">
            {{-- {# Centered on small screens, left-aligned on medium and up. #} --}}
            <button id="inquireButton"
                class="bg-purple-600 text-white text-xl sm:text-2xl px-8 py-4 w-full md:w-auto rounded-lg shadow-md
            hover:bg-purple-700 hover:shadow-lg transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                Inquire Now
            </button>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <p class="text-gray-700">
                <strong class="font-semibold">Sold by:</strong> DressUp Davao Boutique
            </p>
            <p class="text-gray-600 text-sm mt-1">
                Located at Example St., Davao City, Davao Del Sur
            </p>
            <a href="#" class="text-purple-600 hover:underline text-sm mt-2 block">
                View Shop Profile
            </a>
            {{-- {# Added more detailed shop info and a link. #} --}}
        </div>
    </div>
</div>