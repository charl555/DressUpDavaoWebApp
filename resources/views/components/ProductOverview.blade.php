<div class="bg-white">
    <div class="pt-6">
        {{-- <nav aria-label="Breadcrumb">
            <ol role="list" class="mx-auto flex max-w-2xl items-center space-x-2 px-4 sm:px-6 lg:max-w-7xl lg:px-8">
                <li>
                    <div class="flex items-center">
                        <a href="#" class="mr-2 text-sm font-medium text-gray-900">Men</a>
                        <svg width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true"
                            class="h-5 w-4 text-gray-300">
                            <path d="M5.697 4.34L8.98 16.532h1.327L7.025 4.341H5.697z" />
                        </svg>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <a href="#" class="mr-2 text-sm font-medium text-gray-900">Clothing</a>
                        <svg width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true"
                            class="h-5 w-4 text-gray-300">
                            <path d="M5.697 4.34L8.98 16.532h1.327L7.025 4.341H5.697z" />
                        </svg>
                    </div>
                </li>

                <li class="text-sm">
                    <a href="#" aria-current="page" class="font-medium text-gray-500 hover:text-gray-600">Basic Tee
                        6-Pack</a>
                </li>
            </ol>
        </nav> --}}
        <model-viewer alt="3D Model" src="{{ asset('storage/product-models/gown3dmodel-2.glb') }}" auto-rotate
            camera-controls ar shadow-intensity="1" style="width: 100%; height: 500px;">
        </model-viewer>

        <!-- Image gallery -->
        <div class="mx-auto mt-6 max-w-2xl sm:px-6 lg:grid lg:max-w-7xl lg:grid-cols-3 lg:gap-8 lg:px-8">
            {{-- <img
                src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-02-secondary-product-shot.jpg"
                alt="Two each of gray, white, and black shirts laying flat."
                class="row-span-2 aspect-3/4 size-full rounded-lg object-cover max-lg:hidden" />
            <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-02-tertiary-product-shot-01.jpg"
                alt="Model wearing plain black basic tee."
                class="col-start-2 aspect-3/2 size-full rounded-lg object-cover max-lg:hidden" />
            <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-02-tertiary-product-shot-02.jpg"
                alt="Model wearing plain gray basic tee."
                class="col-start-2 row-start-2 aspect-3/2 size-full rounded-lg object-cover max-lg:hidden" />
            <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-02-featured-product-shot.jpg"
                alt="Model wearing plain white basic tee."
                class="row-span-2 aspect-4/5 size-full object-cover sm:rounded-lg lg:aspect-3/4" /> --}}
            <div class="carousel rounded-box h-96 lg:hidden">
                <div class="carousel-item">
                    <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-02-secondary-product-shot.jpg"
                        alt="Burger" />
                </div>
                <div class="carousel-item">
                    <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-02-tertiary-product-shot-01.jpg"
                        alt="Burger" />
                </div>
                <div class="carousel-item">
                    <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-02-tertiary-product-shot-02.jpg"
                        alt="Burger" />
                </div>
                <div class="carousel-item">
                    <img src="https://tailwindcss.com/plus-assets/img/ecommerce-images/product-page-02-featured-product-shot.jpg"
                        alt="Burger" />
                </div>
            </div>
        </div>

        <!-- Product info -->
        <div
            class="mx-auto max-w-2xl px-4 pt-10 pb-16 sm:px-6 lg:grid lg:max-w-7xl lg:grid-cols-3 lg:grid-rows-[auto_auto_1fr] lg:gap-x-8 lg:px-8 lg:pt-16 lg:pb-24">
            <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Product Name</h1>
            </div>

            <!-- Options -->
            <div class="mt-4 lg:row-span-3 lg:mt-0">
                <h2 class="sr-only">Product information</h2>
                <p class="text-2xl tracking-tight text-gray-900 mb-2">Product Status</p>
                <span
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-green-600/20 ring-inset">Available</span>
                <span
                    class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-yellow-600/20 ring-inset">Unavailable</span>
                <form class="mt-10">
                    <button type="submit"
                        class="mt-10 flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">Inquire</button>
                </form>
            </div>

            <div class="py-10 lg:col-span-2 lg:col-start-1 lg:border-r lg:border-gray-200 lg:pt-6 lg:pr-8 lg:pb-16">
                <!-- Description and details -->
                <div>
                    <h3 class="sr-only">Description</h3>

                    <div class="space-y-6">
                        <p class="text-base text-gray-900">Product Status</p>

                    </div>
                </div>

                <div class="mt-10">
                    <h3 class="text-sm font-medium text-gray-900">Inclusions</h3>

                    <div class="mt-4">
                        <ul role="list" class="list-disc space-y-2 pl-4 text-sm">
                            <li class="text-gray-400"><span class="text-gray-600">Hand cut and sewn locally</span>
                            </li>
                            <li class="text-gray-400"><span class="text-gray-600">Dyed with our proprietary
                                    colors</span></li>
                            <li class="text-gray-400"><span class="text-gray-600">Pre-washed &amp; pre-shrunk</span>
                            </li>
                            <li class="text-gray-400"><span class="text-gray-600">Ultra-soft 100% cotton</span></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10">
                    <h2 class="text-sm font-medium text-gray-900">Details</h2>

                    <div class="mt-4 space-y-6">
                        <p class="text-sm text-gray-600">Product Details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>