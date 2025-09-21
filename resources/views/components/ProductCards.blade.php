<div class="bg-white flex flex-col py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-4xl text-center text-black py-10" style="font-family: 'Playfair Display', serif;">
        Collections.
    </h1>

    <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 px-4 justify-items-center lg:justify-items-start">
        @forelse ($products as $product)
            <div class="group cursor-pointer max-w-[300px] w-full"
                onclick="window.location.href='{{ route('product.overview', ['product_id' => $product->product_id]) }}'">

                <div
                    class="h-96 sm:h-[500px] md:h-[500px] lg:h-[500px] xl:h-[500px] w-full border border-gray-300 rounded-md shadow leading-tight focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500
                                            transform transition-transform duration-300 ease-in-out group-hover:-translate-y-1 ">

                    @php

                        $imageRecord = $product->product_images->first();
                    @endphp

                    @if ($imageRecord && $imageRecord->thumbnail_image)
                        <img src="{{ Storage::disk('public')->url($imageRecord->thumbnail_image) }}" alt="{{ $product->name }}"
                            class="h-full w-full object-cover rounded-md shadow-md" />
                    @else
                        <div
                            class="h-full w-full flex items-center justify-center bg-gray-300 text-gray-700 rounded-md shadow-md">
                            Image not available
                        </div>
                    @endif
                </div>
                <div>
                    <p
                        class="text-left text-black text-xl font-semibold
                                                    transition-colors duration-300 ease-in-out group-hover:text-purple-600">

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
    <div class="flex justify-center mb-8">
        <a href="/product-list">
            <div
                class="bg-white w-auto px-6 py-3 flex flex-row items-center justify-center hover:bg-purple-600 not-[]:transition-colors duration-300 ease-in-out border border-purple-600 group">
                <p class="text-purple-600 text-semibold text-center group-hover:text-white mr-2"> View all Collections
                </p>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                    class="size-4 text-purple-600 group-hover:text-white hover:cursor-pointer">
                    <path fill-rule="evenodd"
                        d="M2 8a.75.75 0 0 1 .75-.75h8.69L8.22 4.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H2.75A.75.75 0 0 1 2 8Z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        </a>
    </div>
</div>