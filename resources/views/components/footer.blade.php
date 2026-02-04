<footer class="bg-white text-black pt-24 md:pt-24 pb-8 md:pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">

            <!-- Logo & Socials - Always visible -->
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

            <!-- Company - Collapsible on mobile -->
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

            <!-- Legal - Collapsible on mobile -->
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

            <!-- Quick Links - Collapsible on mobile -->
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

            <!-- Download App Button Section -->
            <div class="md:col-span-1">
                <div class="text-lg font-semibold mb-4 md:mb-4 text-center md:text-left">
                    <span>Get Our App</span>
                </div>

                <!-- Download App Button -->
                <button id="download-app-button"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-download text-lg"></i>
                    <span class="font-semibold">Download App</span>
                </button>

                <!-- Simple Text -->
                <p class="text-xs text-gray-500 mt-3 text-center md:text-left">
                    Install to your home screen
                </p>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="mt-12 border-t border-gray-300 pt-6 text-center text-sm">
            <p>&copy; {{ date('Y') }} DressUp Davao. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Minimal JavaScript for PWA Installation -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const downloadButton = document.getElementById('download-app-button');
        let deferredPrompt = null;

        // Check if PWA is already installed
        const isPWAInstalled = window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true;

        if (isPWAInstalled) {
            downloadButton.innerHTML = '<i class="fas fa-check"></i><span>App Installed</span>';
            downloadButton.classList.remove('bg-purple-600', 'hover:bg-purple-700', 'hover:shadow-lg', 'transform', 'hover:-translate-y-0.5');
            downloadButton.classList.add('bg-green-500', 'cursor-default');
            downloadButton.disabled = true;
        }

        // Listen for beforeinstallprompt event (Chrome, Edge, etc.)
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Update button text
            downloadButton.innerHTML = '<i class="fas fa-download"></i><span>Install Now</span>';
        });

        // Handle button click
        downloadButton.addEventListener('click', function () {
            if (deferredPrompt) {
                // Show browser's native install prompt
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                        downloadButton.innerHTML = '<i class="fas fa-check"></i><span>App Installed</span>';
                        downloadButton.classList.remove('bg-purple-600', 'hover:bg-purple-700', 'hover:shadow-lg', 'transform', 'hover:-translate-y-0.5');
                        downloadButton.classList.add('bg-green-500', 'cursor-default');
                        downloadButton.disabled = true;
                    }
                    deferredPrompt = null;
                });
            } else {
                // Show instructions based on device
                showInstallInstructions();
            }
        });

        // Detect when app is successfully installed
        window.addEventListener('appinstalled', () => {
            downloadButton.innerHTML = '<i class="fas fa-check"></i><span>App Installed</span>';
            downloadButton.classList.remove('bg-purple-600', 'hover:bg-purple-700', 'hover:shadow-lg', 'transform', 'hover:-translate-y-0.5');
            downloadButton.classList.add('bg-green-500', 'cursor-default');
            downloadButton.disabled = true;
        });

        // Simple install instructions
        function showInstallInstructions() {
            const userAgent = navigator.userAgent.toLowerCase();
            let message = '';

            if (/iphone|ipad|ipod/.test(userAgent)) {
                message = 'To install: Tap Share (⎙) → "Add to Home Screen" → "Add"';
            } else if (/android/.test(userAgent)) {
                message = 'To install: Tap Menu (⋮) → "Add to Home screen" → "Add"';
            } else {
                message = 'To install: Look for the install icon (⊕) in your browser\'s address bar';
            }

            alert('Install DressUp Davao App\n\n' + message);
        }

        // Keep your existing accordion functionality
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
    });
</script>

<style>
    /* Smooth transitions for the accordion */
    .accordion-content {
        transition: all 0.3s ease-in-out;
    }

    /* Button hover effects */
    #download-app-button {
        transition: all 0.2s ease;
    }

    /* Ensure proper spacing on mobile */
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