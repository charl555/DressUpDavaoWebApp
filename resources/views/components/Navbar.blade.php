<div id="navbar"
    class="navbar-container fixed top-0 left-0 w-full z-50 transition-transform duration-300 ease-in-out bg-white text-black">
    <div class=" grid grid-cols-3 justify-items-center px-4 py-2 sm:px-6 lg:px-10 ">
        <div>

        </div>
        <div class="flex items-center">
            <a href="/">
                <img src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao" class="h-16 w-auto" />
            </a>
        </div>
        <div class="flex items-center">
            {{-- User Dropdown --}}
            <div class="relative px-3" x-data="{ open: false }">
                @guest
                    <!-- Show Sign In button for guests -->
                    <a href="/login"
                        class="px-4 py-2 bg-white text-black rounded-md border-1.5 box-shadow shadow-md hover:bg-purple-700 hover:text-white transition-colors duration-300">
                        Sign in
                    </a>
                @endguest

                @auth
                    <!-- User Icon for logged-in users -->
                    <button @click="open = !open" class="focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 
                                                                                                   20.118a7.5 7.5 0 0 1 14.998 0A17.933 
                                                                                                   17.933 0 0 1 12 21.75c-2.676 
                                                                                                   0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-cloak x-show="open" @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 
                                                                                               rounded-lg shadow-lg py-2 z-50">
                        <a href="/account" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Account</a>
                        <a href="/bookings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Bookings</a>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                Log Out
                            </button>
                        </form>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden ml-4">
                <button id="mobile-menu-button" class="text-black focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

    </div>
    <div class="hidden items-center justify-center lg:flex ">
        <ol class="flex justify-items justify-center p-10 gap-6">
            <li>
                <a href="#"
                    class="mx-4 font-italic hover:text-purple-900 transition-colors duration-300 ease-in-out hover-underline-animation">Gowns</a>
            </li>
            <li>
                <a href="#"
                    class="mx-4 font-italic hover:text-purple-900 transition-colors duration-300 ease-in-out hover-underline-animation">Suits</a>
            </li>
            <li>
                <a href="/product-list"
                    class="mx-4 font-italic hover:text-purple-900 transition-colors duration-300 ease-in-out hover-underline-animation">Collections
                </a>
            </li>
            <li>
                <a href="/shops"
                    class="mx-4 font-italic hover:text-purple-900 transition-colors duration-300 ease-in-out hover-underline-animation">Shops</a>
            </li>
            <li>
                <a href="/admin/login"
                    class="mx-4 font-italic hover:text-purple-900 transition-colors duration-300 ease-in-out hover-underline-animation">Shop
                    Center</a>
            </li>
        </ol>
    </div>

    <div id="mobile-menu" class="lg:hidden absolute top-full left-0 w-full bg-white shadow-md py-0 overflow-hidden
   max-h-0 opacity-0 transition-all duration-300 ease-in-out z-50">
        <ol class="flex flex-col items-center">
            <li>
                <a href="#"
                    class="block px-4 py-2 font-semibold text-black hover:text-purple-900 transition-colors duration-300 ease-in-out">Gowns</a>
            </li>
            <li>
                <a href="#"
                    class="block px-4 py-2 font-semibold text-black hover:text-purple-900 transition-colors duration-300 ease-in-out">Suits</a>
            </li>
            <li>
                <a href="#"
                    class="block px-4 py-2 font-semibold text-black hover:text-purple-900 transition-colors duration-300 ease-in-out">Shops</a>
            </li>
            <li>
                <a href="#"
                    class="block px-4 py-2 font-semibold text-black hover:text-purple-900 transition-colors duration-300 ease-in-out">Shop
                    Center</a>
            </li>
        </ol>
    </div>
</div>
<script src="https://cdn.tailwindcss.com"></script>
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuButton.addEventListener('click', function () {

            if (mobileMenu.classList.contains('opacity-0')) {
                // Opening the menu
                mobileMenu.classList.remove('opacity-0', 'max-h-0');
                mobileMenu.classList.add('opacity-100', 'max-h-screen', 'py-4');
            } else {
                // Closing the menu
                mobileMenu.classList.remove('opacity-100', 'max-h-screen', 'py-4');
                mobileMenu.classList.add('opacity-0', 'max-h-0');
            }
        });
        document.addEventListener('click', function (event) {
            if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                if (mobileMenu.classList.contains('opacity-100')) {
                    mobileMenu.classList.remove('opacity-100', 'max-h-screen', 'py-4');
                    mobileMenu.classList.add('opacity-0', 'max-h-0');
                }
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
        let lastScrollTop = 0;
        const navbar = document.getElementById('navbar');
        const scrollThreshold = 150; // set your desired threshold in pixels

        window.addEventListener('scroll', function () {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            // Only apply hiding logic after crossing the threshold
            if (currentScroll > scrollThreshold) {
                if (currentScroll > lastScrollTop) {
                    // Scrolling down
                    navbar.classList.add('navbar-hidden');
                } else {
                    // Scrolling up
                    navbar.classList.remove('navbar-hidden');
                }
            } else {
                // If not past the threshold, always show navbar
                navbar.classList.remove('navbar-hidden');
            }

            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // avoid negative scroll
        });
    });
</script>
<style>
    .hover-underline-animation {
        position: relative;
        text-decoration: none;
        padding-bottom: 2px;
    }

    .hover-underline-animation::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: #5b21b6;
        /* purple-900 */
        transform: scaleX(0);
        transform-origin: bottom left;
        transition: transform 0.3s ease-in-out;
    }

    .hover-underline-animation:hover::after {
        transform: scaleX(1);
    }

    .navbar-container {
        transform: translateY(0);
    }

    .navbar-hidden {
        transform: translateY(-100%);
    }
</style>