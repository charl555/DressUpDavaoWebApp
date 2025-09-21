<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 px-4 sm:px-6 lg:px-8 pt-[200px] pb-12 max-w-7xl mx-auto">
    {{-- Left: Images --}}
    <div class="flex flex-col">
        {{-- Main thumbnail --}}
        @php
            $thumbnail = $product->product_images->first()?->thumbnail_image;
        @endphp
        <div class="bg-gray-100 rounded-lg overflow-hidden shadow-lg aspect-video w-full h-[600px] mb-4">
            @if ($thumbnail)
                <img src="{{ Storage::disk('public')->url($thumbnail) }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover cursor-pointer"
                    onclick="openImageModal('{{ Storage::disk('public')->url($thumbnail) }}')" />
            @else
                <div class="flex items-center justify-center h-full w-full text-gray-500">No Image</div>
            @endif
        </div>

        {{-- Gallery --}}
        <div class="grid grid-cols-4 gap-2">
            @foreach ($product->product_images as $img)
                <div
                    class="bg-gray-100 h-24 sm:h-32 rounded-md overflow-hidden cursor-pointer hover:ring-2 hover:ring-purple-500 transition-all duration-200 group">
                    <img src="{{ Storage::disk('public')->url($img->image) }}" alt="Product Image"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                        onclick="openImageModal('{{ Storage::disk('public')->url($img->image) }}')" />
                </div>
            @endforeach
        </div>
    </div>

    {{-- Right: Product Info --}}
    <div class="flex flex-col pt-8 lg:pt-0">
        <div class="pb-6 border-b border-gray-200 mb-6">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight"
                style="font-family: 'Playfair Display', serif;">
                {{ $product->name }}
            </h1>
        </div>

        <div class="space-y-4 mb-8 text-base text-gray-700 leading-relaxed">
            <p>{{ $product->description ?? 'No description available.' }}</p>
            <p><span class="font-semibold">Inclusions:</span> {{ $product->inclusions ?? 'N/A' }}</p>
            <p><span class="font-semibold">Type:</span> {{ $product->type }}</p>
            <p><span class="font-semibold">Style:</span> {{ $product->subtype ?? 'N/A' }}</p>
            <p><span class="font-semibold">Size:</span> {{ $product->size }}</p>
            <p><span class="font-semibold">Colors:</span> {{ $product->colors }}</p>


            {{-- Conditional Measurements --}}
            @if ($product->type === 'Gown')
                <h2 class="text-xl font-semibold text-gray-900 mt-6">Measurements (Gown)</h2>
                <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                    <p><span class="font-medium">Length:</span> {{ $product->product_measurements->gown_length ?? 'N/A' }}
                        in</p>
                    <p><span class="font-medium">Upper Chest:</span>
                        {{ $product->product_measurements->gown_upper_chest ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Chest:</span> {{ $product->product_measurements->gown_chest ?? 'N/A' }} in
                    </p>
                    <p><span class="font-medium">Waist:</span> {{ $product->product_measurements->gown_waist ?? 'N/A' }} in
                    </p>
                    <p><span class="font-medium">Hips:</span> {{ $product->product_measurements->gown_hips ?? 'N/A' }} in
                    </p>
                </div>
            @elseif ($product->type === 'Suit')
                <h2 class="text-xl font-semibold text-gray-900 mt-6">Measurements (Suit)</h2>
                <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                    <p><span class="font-medium">Jacket Chest:</span>
                        {{ $product->product_measurements->jacket_chest ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Jacket Length:</span>
                        {{ $product->product_measurements->jacket_length ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Shoulder:</span>
                        {{ $product->product_measurements->jacket_shoulder ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Sleeve Length:</span>
                        {{ $product->product_measurements->jacket_sleeve_length ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Sleeve Width:</span>
                        {{ $product->product_measurements->jacket_sleeve_width ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Bicep:</span> {{ $product->product_measurements->jacket_bicep ?? 'N/A' }}
                        in</p>
                    <p><span class="font-medium">Arm Hole:</span>
                        {{ $product->product_measurements->jacket_arm_hole ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Jacket Waist:</span>
                        {{ $product->product_measurements->jacket_waist ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Trouser Waist:</span>
                        {{ $product->product_measurements->trouser_waist ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Trouser Hip:</span>
                        {{ $product->product_measurements->trouser_hip ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Inseam:</span>
                        {{ $product->product_measurements->trouser_inseam ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Outseam:</span>
                        {{ $product->product_measurements->trouser_outseam ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Thigh:</span> {{ $product->product_measurements->trouser_thigh ?? 'N/A' }}
                        in</p>
                    <p><span class="font-medium">Leg Opening:</span>
                        {{ $product->product_measurements->trouser_leg_opening ?? 'N/A' }} in</p>
                    <p><span class="font-medium">Crotch:</span>
                        {{ $product->product_measurements->trouser_crotch ?? 'N/A' }} in</p>
                </div>
            @endif

        </div>

        <div class="flex justify-center md:justify-start mb-8">
            <button id="inquireButton" class="bg-purple-600 text-white text-lg sm:text-xl px-6 sm:px-8 py-3 sm:py-4 w-full md:w-auto rounded-lg shadow-md
                hover:bg-purple-700 hover:shadow-lg transition-all duration-300 ease-in-out">
                Inquire Now
            </button>
        </div>

        <div class="border-t border-gray-200 pt-6 text-sm sm:text-base">
            <p><span class="font-semibold">Sold by:</span> {{ $product->user->shop->shop_name }}</p>
            <p class="text-gray-600 mt-1">{{ $product->user->shop->shop_address ?? 'No address provided' }}</p>
            <a href="{{ route('shop.overview', $product->user->shop) }}"
                class="text-purple-600 hover:underline mt-2 block">
                View Shop Profile
            </a>
        </div>
    </div>
</div>

{{-- Pass product data to JavaScript --}}
<script>
    window.productData = {
        id: {{ $product->product_id }},
        name: @json($product->name),
        owner: @json($product->user->name),
        shop: @json($product->user->shop->shop_name ?? 'Unknown Shop')
    };
</script>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
    <div class="relative max-w-4xl w-full mx-4">
        <button onclick="closeImageModal()"
            class="absolute top-3 right-3 bg-white rounded-full p-2 shadow hover:bg-gray-200 transition">
            ✕
        </button>
        <img id="modalImage" src="" class="w-full max-h-[80vh] object-contain rounded-lg shadow-lg" />
    </div>
</div>

<script>
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
    }
    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
    }
    // Close modal when clicking outside image
    document.getElementById('imageModal').addEventListener('click', function (e) {
        if (e.target.id === 'imageModal') {
            closeImageModal();
        }
    });
</script>