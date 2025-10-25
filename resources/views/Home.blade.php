<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Homepage - Dress Up Davao</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- Preload critical fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap"
        as="style">

    <!-- Preload critical images -->
    <link rel="preload" href="{{ asset('frontend-images/gown-backdrop.webp') }}" as="image">
    <link rel="preload" href="{{ asset('frontend-images/gown-category.webp') }}" as="image">
    <link rel="preload" href="{{ asset('frontend-images/suit-category.webp') }}" as="image">

    <!-- Inline critical CSS -->
    <style>
        /* Critical above-the-fold styles */
        .bg-black {
            background-color: #000;
        }

        .text-white {
            color: #fff;
        }

        .flex {
            display: flex;
        }

        .w-full {
            width: 100%;
        }

        .h-\[500px\] {
            height: 500px;
        }

        .absolute {
            position: absolute;
        }

        .inset-0 {
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .justify-center {
            justify-content: center;
        }

        .items-center {
            align-items: center;
        }

        .z-10 {
            z-index: 10;
        }

        /* Skeleton styles */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        .skeleton-text,
        .skeleton-image,
        .skeleton-button {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>


    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap">
</head>

<body class="bg-white min-h-screen flex flex-col">
    <main class="flex-grow">
        <x-Navbar />
        <x-Hero />
        {{-- <x-ThreeDimensionalAssetViewer /> --}}
        <x-InfoCardsComponent />
        <x-Category />
        <x-ProductCards :products="$products" />
    </main>

    <x-Footer />
    <x-Chatwindow />
    <x-Toast />


    <script src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js" defer></script>


    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));

            if ('IntersectionObserver' in window) {
                const lazyImageObserver = new IntersectionObserver(function (entries, observer) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            const lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.classList.remove('lazy');
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function (lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            }


            if ('requestIdleCallback' in window) {
                requestIdleCallback(() => {
                    const preloadLinks = ['/product-list', '/about'];
                    preloadLinks.forEach(link => {
                        const preloadLink = document.createElement('link');
                        preloadLink.rel = 'prefetch';
                        preloadLink.href = link;
                        document.head.appendChild(preloadLink);
                    });
                });
            }
        });
    </script>
</body>

</html>