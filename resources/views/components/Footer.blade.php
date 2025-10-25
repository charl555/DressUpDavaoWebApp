<footer class="bg-white text-black pt-24 md:pt-24 pb-8 md:pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">

            <!-- Logo & Socials -->
            <div class="col-span-1 flex flex-col items-start">
                <div class="mb-6">
                    <img src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao Logo"
                        class="h-20 w-auto md:h-24 lg:h-28">
                </div>
                <p class="text-base mb-6 leading-relaxed">
                    Wear the moment.<br>Rent with Ease.
                </p>
                <div class="flex space-x-4">
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
            <div>
                <h4 class="text-lg font-semibold mb-4">Company</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">About Us</a></li>
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">Contact</a></li>
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">FAQs</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Legal</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">Terms & Services</a></li>
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">Terms of Use</a></li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">How It Works</a></li>
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">Downloads</a></li>
                    <li><a href="#" class="hover:text-purple-600 transition duration-300">Forum</a></li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        {{-- <div class="mt-12 border-t border-gray-300 pt-6 text-center text-sm">
            <p>&copy; {{ date('Y') }} DressUp Davao. All rights reserved.</p>
        </div> --}}
    </div>
</footer>