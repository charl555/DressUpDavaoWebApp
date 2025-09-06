<div class="bg-white flex flex-col py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-4xl text-center text-black py-10" style="font-family: 'Playfair Display', serif;">
        Collections.
    </h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
        <div class="group cursor-pointer">
            <div class="h-96 sm:h-[500px] md:h-[500px] lg:h-[500px] xl:h-[500px] w-full border border-gray-300 rounded-md shadow leading-tight focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500
                        transform transition-transform duration-300 ease-in-out group-hover:-translate-y-1">
                <img src="{{ asset('frontend-images/gown-photo.jpg') }}" alt="Product Image"
                    class="h-full w-full object-cover rounded-md shadow-md" />
            </div>
            <div>
                <p class="text-left text-black text-xl font-semibold
                                transition-colors duration-300 ease-in-out group-hover:text-purple-600">

                    Lavander
                </p>
                <p class="text-left text-gray-600 text-base">Ball</p>
                <p class="text-left text-gray-600 text-base">Lavander</p>
                <p class="text-left text-gray-600 text-base">Medium</p>
            </div>
        </div>
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