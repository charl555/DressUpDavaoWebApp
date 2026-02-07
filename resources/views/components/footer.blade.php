@php
    // Detect if request is from Android app
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
@endphp

@unless($isMobileApp)
    <footer class="bg-white text-black pt-24 md:pt-24 pb-8 md:pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-8">

                <!-- Logo & Socials -->
                <div class="col-span-1 flex flex-col items-center md:items-start text-center md:text-left">
                    <div class="mb-6">
                        <a href="/">
                            <img src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao Logo"
                                class="h-20 w-auto md:h-24 lg:h-28 mx-auto md:mx-0"></a>
                    </div>
                    <p class="text-base mb-6 leading-relaxed">
                        Wear the moment.<br>Rent with Ease.
                    </p>
                    <div class="flex space-x-4 justify-center md:justify-start">
                        <a href="#" class="text-black hover:text-purple-600 transition duration-300" aria-label="Email">
                            <i class="fas fa-envelope text-2xl"></i>
                        </a>
                        <a href="#" class="text-black hover:text-purple-600 transition duration-300" aria-label="Phone">
                            <i class="fas fa-phone text-2xl"></i>
                        </a>
                        <a href="#" class="text-black hover:text-purple-600 transition duration-300" aria-label="Instagram">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                        <a href="#" class="text-black hover:text-purple-600 transition duration-300" aria-label="Facebook">
                            <i class="fab fa-facebook-f text-2xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Company -->
                <div class="md:col-span-1">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center md:justify-start md:cursor-default text-lg font-semibold mb-4 md:mb-4">
                        <span>Company</span>
                        <span class="md:hidden transform transition-transform duration-300">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </button>
                    <ul class="accordion-content space-y-3 text-sm text-center md:text-left hidden md:block">
                        <li><a href="/about-us" class="hover:text-purple-600 transition duration-300 block py-1">About
                                Us</a>
                        </li>
                        <li><a href="/contact" class="hover:text-purple-600 transition duration-300 block py-1">Contact</a>
                        </li>
                        <li><a href="/faq" class="hover:text-purple-600 transition duration-300 block py-1">FAQs</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div class="md:col-span-1">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center md:justify-start md:cursor-default text-lg font-semibold mb-4 md:mb-4">
                        <span>Legal</span>
                        <span class="md:hidden transform transition-transform duration-300">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </button>
                    <ul class="accordion-content space-y-3 text-sm text-center md:text-left hidden md:block">
                        <li><a href="/privacy-policy"
                                class="hover:text-purple-600 transition duration-300 block py-1">Privacy Policy</a>
                        </li>
                        <li><a href="/terms-and-services"
                                class="hover:text-purple-600 transition duration-300 block py-1">Terms &
                                Services</a></li>
                        <li><a href="/terms-of-use" class="hover:text-purple-600 transition duration-300 block py-1">Terms
                                of
                                Use</a>
                        </li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div class="md:col-span-1">
                    <button
                        class="accordion-toggle w-full flex justify-between items-center md:justify-start md:cursor-default text-lg font-semibold mb-4 md:mb-4">
                        <span>Quick Links</span>
                        <span class="md:hidden transform transition-transform duration-300">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </button>
                    <ul class="accordion-content space-y-3 text-sm text-center md:text-left hidden md:block">
                        <li><a href="/how-it-works" class="hover:text-purple-600 transition duration-300 block py-1">How It
                                Works</a>
                        </li>
                        <li><a href="/downloads"
                                class="hover:text-purple-600 transition duration-300 block py-1">Downloads</a>
                        </li>
                        <li><a href="/forum" class="hover:text-purple-600 transition duration-300 block py-1">Forum</a></li>
                    </ul>
                </div>

                <!-- 🔥 UPDATED: APK Download Section -->
                <div class="md:col-span-1">
                    <div class="text-lg font-semibold mb-4 text-center md:text-left">
                        <span>Get Our App</span>
                    </div>

                    <!-- APK Download Button -->
                    <a href="/downloads/DressUp.apk" download="DressUpDavao.apk"
                        class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-3">
                        <i class="fab fa-android text-lg"></i>
                        <span class="font-semibold">Download App</span>
                    </a>


                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="mt-12 border-t border-gray-300 pt-6 text-center text-sm">
                <p>&copy; {{ date('Y') }} DressUp Davao. All rights reserved.</p>

            </div>
        </div>
    </footer>
@endunless

<!-- JavaScript for collapsible functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const accordionToggles = document.querySelectorAll('.accordion-toggle');

        function initializeAccordions() {
            if (window.innerWidth < 768) {
                document.querySelectorAll('.accordion-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.querySelectorAll('.accordion-toggle i').forEach(icon => {
                    icon.style.transform = 'rotate(0deg)';
                });
            } else {
                document.querySelectorAll('.accordion-content').forEach(content => {
                    content.classList.remove('hidden');
                });
            }
        }

        initializeAccordions();

        accordionToggles.forEach(toggle => {
            toggle.addEventListener('click', function () {
                if (window.innerWidth < 768) {
                    const content = this.nextElementSibling;
                    const icon = this.querySelector('i');
                    content.classList.toggle('hidden');
                    icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            });
        });

        window.addEventListener('resize', initializeAccordions);

        // APK Download Analytics
        document.querySelector('a[href*="DressUp.apk"]').addEventListener('click', function () {
            // You can add analytics here
            console.log('APK download initiated');
            // Example: fetch('/api/track-download', { method: 'POST' });
        });
    });
</script>

<style>
    .accordion-content {
        transition: all 0.3s ease-in-out;
    }

    @media (max-width: 767px) {
        .accordion-toggle {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
        }

        .accordion-content {
            padding-top: 0.5rem;
            padding-bottom: 1rem;
        }
    }
</style>