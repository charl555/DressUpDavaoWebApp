<div
    class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
    @forelse ($products as $product)
        <div class="group cursor-pointer max-w-[400px] w-full relative"
            onclick="window.location.href='{{ route('product.overview', ['product_id' => $product->product_id]) }}'">


            <div
                class="relative h-96 sm:h-[400px] md:h-[400px] lg:h-[400px] xl:h-[400px] w-full transform transition-transform duration-300 ease-in-out group-hover:-translate-y-1">
                @php
                    $imageRecord = $product->product_images->first();
                @endphp

                @if ($imageRecord && $imageRecord->thumbnail_image)
                    <img src="{{ asset('storage/' . $imageRecord->thumbnail_image) }}" alt="{{ $product->name }}"
                        class="h-full w-full object-cover rounded-md shadow-md" />
                @else
                    <div class="h-full w-full flex items-center justify-center bg-gray-300 text-gray-700 rounded-md shadow-md">
                        Image not available
                    </div>
                @endif


                @if ($product->status !== 'Available')
                    <div class="absolute inset-0 bg-gray-900/60 flex items-center justify-center rounded-md">
                        <span class="text-white text-lg font-semibold tracking-wide">Not Available</span>
                    </div>
                @endif


            </div>

            <div class="mt-2 space-y-1">
                <p
                    class="text-left text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors duration-300">
                    {{ $product->name }}
                </p>
                <p class="text-left text-gray-600 text-sm">{{ $product->subtype }}</p>
                <p class="text-left text-gray-600 text-sm">{{ $product->size }}</p>
                <p class="text-left text-gray-500 text-xs italic">{{ $product->user->shop->shop_name }}</p>
            </div>
        </div>
    @empty
        <p class="col-span-full text-center text-gray-500">No products available.</p>
    @endforelse
</div>

@if ($products->hasPages())
    <div class="px-4 mb-8">
        {{ $products->links() }}
    </div>
@endif