{{-- <div class="flex flex-col lg:flex-row text-black p-10 gap-10 justify-items-center items-center lg:justify-center">
    <div class="w-[300px] lg:w-[400px] aspect-square flex flex-col items-center justify-center 
                shadow-md cursor-pointer relative overflow-hidden
                group"
        style="background-image: url('{{ asset('frontend-images/gown-category.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black bg-opacity-30 ">
        </div>
        <h1 class="text-4xl text-white z-10 relative 
                   group-hover:text-purple-600 transition-colors duration-300 ease-in-out"
            style="font-family: 'Playfair Display', serif;">
            Gowns
        </h1>
    </div>

    <div class="w-[300px] lg:w-[400px]  aspect-square flex flex-col items-center justify-center 
                shadow-md cursor-pointer relative overflow-hidden
                group"
        style="background-image: url('{{ asset('frontend-images/suit-category.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black bg-opacity-30 ">
        </div>
        <h1 class="text-4xl text-white z-10 relative
                   group-hover:text-purple-600 transition-colors duration-300 ease-in-out"
            style="font-family: 'Playfair Display', serif;">
            Suits
        </h1>
    </div>
</div> --}}
<div class="py-16 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 justify-items-center">

        {{-- Gowns Card --}}
        <div
            class="w-full max-w-lg bg-white rounded-xl overflow-hidden shadow-lg transform transition-transform duration-300 hover:scale-[1.01] cursor-pointer">
            <div class="relative aspect-video">
                <img src="{{ asset('frontend-images/gown-category.jpg') }}" alt="Gowns"
                    class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                <div class="absolute inset-x-0 bottom-4 text-white p-4">
                    <h2 class="text-3xl font-bold" style="font-family: 'Playfair Display', serif;">Gowns</h2>
                    <p class="text-sm mt-1">Wedding, Evening & Formal</p>
                    <a href="#"
                        class="mt-4 inline-flex items-center space-x-2 text-white border-b-2 border-purple-600 hover:text-purple-600 transition-colors duration-300 ease-in-out">
                        <span>Explore Gowns</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Suits Card --}}
        <div
            class="w-full max-w-lg bg-white rounded-xl overflow-hidden shadow-lg transform transition-transform duration-300 hover:scale-[1.01] cursor-pointer">
            <div class="relative aspect-video">
                <img src="{{ asset('frontend-images/suit-category.jpg') }}" alt="Suits"
                    class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                <div class="absolute inset-x-0 bottom-4 text-white p-4">
                    <h2 class="text-3xl font-bold" style="font-family: 'Playfair Display', serif;">Suits</h2>
                    <p class="text-sm mt-1">Tuxedos, Business & Casual</p>
                    <a href="#"
                        class="mt-4 inline-flex items-center space-x-2 text-white border-b-2 border-purple-600 hover:text-purple-600 transition-colors duration-300 ease-in-out">
                        <span>Explore Suits</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>