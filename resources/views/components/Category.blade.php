<div class="flex flex-col lg:flex-row text-black p-10 gap-10 justify-items-center items-center lg:justify-center">
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
</div>