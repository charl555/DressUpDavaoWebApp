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


                <div class="md:col-span-1">
                    <div class="text-lg font-semibold mb-4 text-center md:text-left">
                        <span class="flex items-center">
                            <i class="fab fa-android text-purple-600 mr-2"></i>
                            Android App
                        </span>
                    </div>

                    <!-- Android App Download Button (Hero-style) -->
                    <a href="/downloads/DressUpDavao.apk" download="DressUpDavao.apk"
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 ease-out group border-0 w-full md:w-auto">
                        <!-- Android Icon -->
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4483-.9993.9993-.9993c.5511 0 .9993.4483.9993.9993.0001.5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4483.9993.9993 0 .5511-.4483.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.2439 13.8533 7.8508 12 7.8508s-3.5902.3931-5.1692 1.0992L4.8085 5.4471a.4161.4161 0 00-.5677-.1521.4157.4157 0 00-.1521.5676l1.9973 3.4592C2.8349 9.4514 1 12.1863 1 15.32v.08c0 .286.214.5.5.5h21c.286 0 .5-.214.5-.5v-.08c0-3.1337-1.8349-5.8686-4.523-6.0586" />
                        </svg>

                        <span class="mr-2 font-semibold">Download Android App</span>

                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>

                    <!-- Version & Size Info -->
                    <div class="mt-3 text-xs text-gray-500 text-center md:text-left">
                        <p>v1.0.0 • 15 MB • Android 5.0+</p>
                    </div>
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
        const downloadBtn = document.querySelector('a[href*="DressUpDavao.apk"]');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function (e) {
                // Track download event
                console.log('Android app download initiated');

                // You can add analytics here:
                // fetch('/api/track-download', { 
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/json' },
                //     body: JSON.stringify({ 
                //         type: 'android_app',
                //         version: '1.0.0',
                //         timestamp: new Date().toISOString()
                //     })
                // });

                // Optional: Show download confirmation
                const downloadModal = document.createElement('div');
                downloadModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
                downloadModal.innerHTML = `
                    <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
                        <h3 class="text-lg font-semibold mb-2">Download Started</h3>
                        <p class="text-gray-600 mb-4">Your Android app download has started. If it doesn't start automatically, check your downloads folder.</p>
                        <button onclick="this.parentElement.parentElement.remove()" 
                                class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">
                            OK
                        </button>
                    </div>
                `;
                document.body.appendChild(downloadModal);
                downloadModal.classList.remove('hidden');

                // Auto-close after 3 seconds
                setTimeout(() => {
                    if (downloadModal.parentElement) {
                        downloadModal.remove();
                    }
                }, 3000);
            });
        }
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

    /* Android download button specific styles */
    .android-download-btn {
        background: linear-gradient(135deg, #8B5CF6 0%, #6366F1 100%);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .android-download-btn:hover {
        background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
    }
</style>