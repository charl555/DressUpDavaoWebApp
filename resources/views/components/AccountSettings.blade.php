<div class="flex flex-col lg:flex-row text-black pt-[250px] justify-center lg:space-x-12 px-4 sm:px-6 lg:px-8">
    {{-- {# Main container for the account page. Adjusted padding for better spacing on larger screens. #}
    {# Added lg:space-x-12 to create horizontal space between the sidebar and content on large screens. #} --}}

    <div class="w-full lg:w-1/4 flex flex-col mb-8 lg:mb-0">
        {{-- {# Adjusted width for sidebar on large screens and added margin-bottom for small screens. #} --}}

        <div class="flex items-center space-x-4 pb-8 border-b border-gray-200">
            {{-- {# Used space-x-4 for spacing between avatar and name. #} --}}
            <div>
                <img src="{{ asset('frontend-images/gown-category.jpg') }}" alt="Profile Picture"
                    class="h-20 w-20 rounded-full object-cover shadow-md" />
            </div>
            <div>
                <h1 class="text-2xl font-bold">John Doe</h1>
                {{-- <p class="text-gray-600">john.doe@example.com</p> {# Example: Add user email or other detail #}
                --}}
            </div>
        </div>

        <div class="pt-8">
            <div class="relative">
                <button id="myAccountDropdownBtn"
                    class="flex items-center justify-between w-full text-left text-xl font-bold pb-2 focus:outline-none">
                    My Account
                    <svg class="w-5 h-5 ml-2 transform transition-transform duration-200" id="dropdownArrow" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="myAccountDropdownContent"
                    class="hidden flex-col pl-4 mt-2 space-y-2 transition-all duration-300 ease-in-out origin-top">
                    {{-- {# Hidden by default, will be toggled by JS. space-y-2 for vertical spacing. #} --}}
                    <a href="#"
                        class="nav-link text-black hover:text-purple-700 transition-colors duration-300 ease-in-out active"
                        data-target="profile-settings">Edit Profile</a>
                    <a href="#"
                        class="nav-link text-black hover:text-purple-700 transition-colors duration-300 ease-in-out"
                        data-target="change-password">Change Password</a>
                    <a href="#"
                        class="nav-link text-black hover:text-purple-700 transition-colors duration-300 ease-in-out"
                        data-target="delete-account">Delete Account</a>
                    <a href="#"
                        class="nav-link text-black hover:text-purple-700 transition-colors duration-300 ease-in-out"
                        data-target="my-measurements">My Measurements</a>
                    <a href="#"
                        class="nav-link text-black hover:text-purple-700 transition-colors duration-300 ease-in-out"
                        data-target="my-bookings">Bookings</a>

                    {{-- {# Added data-target attributes to link to content sections #} --}}
                </div>
            </div>

            {{-- {# Example of another static link outside the dropdown, if needed #} --}}
            <div class="pt-6">
                <a href="#"
                    class="text-xl font-bold text-black hover:text-purple-700 transition-colors duration-300 ease-in-out">Bookings</a>
            </div>



        </div>
    </div>

    <div class="w-full lg:w-3/4 bg-white p-8 rounded-lg shadow-md">
        {{-- {# Main content area, takes 3/4 width on large screens, background, padding, rounded corners, shadow. #}
        --}}

        <div id="profile-settings" class="content-section active">
            {{-- {# 'active' class to show this section by default #} --}}
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">My Profile</h1>
            <p class="text-gray-600 pb-8">Manage and protect your account information.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                {{-- {# Used a grid for a cleaner, responsive form layout #} --}}
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                        value="john_doe">
                </div>

                <div>
                    <label for="full-name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="full-name"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                        value="John Doe">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                        value="john.doe@example.com">
                </div>

                <div>
                    <label for="phone-number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" id="phone-number"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                        value="+63 9XX XXX XXXX">
                </div>

                <div class="md:col-span-2 flex flex-col items-center pt-6">
                    {{-- {# md:col-span-2 makes it span both columns on medium screens #} --}}
                    <img src="{{ asset('frontend-images/gown-category.jpg') }}" alt="Profile Picture"
                        class="h-40 w-40 rounded-full object-cover shadow-lg border-4 border-white ring-2 ring-gray-200" />
                    <button
                        class="mt-6 px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        Select Image
                    </button>
                </div>
            </div>

            <div class="pt-8 flex justify-end">
                <button
                    class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-300">
                    Save Changes
                </button>
            </div>
        </div>

        <div id="change-password" class="content-section hidden">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">Change Password</h1>
            <p class="text-gray-600 pb-8">Update your account password.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div>
                    <label for="current-password" class="block text-sm font-medium text-gray-700">Current
                        Password</label>
                    <input type="password" id="current-password"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
                <div></div>
                {{-- {# Empty div to keep alignment in grid #} --}}
                <div>
                    <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" id="new-password"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
                <div>
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm New
                        Password</label>
                    <input type="password" id="confirm-password"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
            </div>
            <div class="pt-8 flex justify-end">
                <button
                    class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-300">
                    Update Password
                </button>
            </div>
        </div>

        <div id="delete-account" class="content-section hidden">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">Delete Account</h1>
            <p class="text-gray-600 pb-8">Permanently delete your account. This action cannot be undone.</p>

            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-6">
                <p class="font-bold">Warning:</p>
                <p>Deleting your account will remove all your data and preferences.</p>
            </div>

            <div class="flex flex-col space-y-4">
                <div>
                    <label for="delete-confirmation" class="block text-sm font-medium text-gray-700">Type "DELETE" to
                        confirm:</label>
                    <input type="text" id="delete-confirmation"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                </div>
                <div>
                    <label for="delete-password" class="block text-sm font-medium text-gray-700">Enter your password to
                        confirm:</label>
                    <input type="password" id="delete-password"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                </div>
            </div>

            <div class="pt-8 flex justify-end">
                <button
                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-300">
                    Delete My Account
                </button>
            </div>
        </div>

        <div id="my-measurements" class="content-section hidden">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">My Measurements</h1>
            <p class="text-gray-600 pb-8">Enter your body measurements to help us recommend the best fit.</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8 items-start">
                {{-- {# Grid for measurements form on left and image on right #} --}}

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    {{-- {# Inner grid for measurement fields for better organization #} --}}

                    <div>
                        <label for="bust-chest" class="block text-sm font-medium text-gray-700">Bust/Chest
                            (inches)</label>
                        <input type="number" id="bust-chest" placeholder="e.g., 36"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="waist" class="block text-sm font-medium text-gray-700">Waist (inches)</label>
                        <input type="number" id="waist" placeholder="e.g., 28"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="hips" class="block text-sm font-medium text-gray-700">Hips (inches)</label>
                        <input type="number" id="hips" placeholder="e.g., 40"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="shoulder" class="block text-sm font-medium text-gray-700">Shoulder to Shoulder
                            (inches)</label>
                        <input type="number" id="shoulder" placeholder="e.g., 16"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="sleeve-length" class="block text-sm font-medium text-gray-700">Sleeve Length
                            (inches)</label>
                        <input type="number" id="sleeve-length" placeholder="e.g., 24"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="inseam" class="block text-sm font-medium text-gray-700">Inseam (inches)</label>
                        <input type="number" id="inseam" placeholder="e.g., 30"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="thigh" class="block text-sm font-medium text-gray-700">Thigh (inches,
                            optional)</label>
                        <input type="number" id="thigh" placeholder="e.g., 22"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="bicep" class="block text-sm font-medium text-gray-700">Bicep (inches,
                            optional)</label>
                        <input type="number" id="bicep" placeholder="e.g., 12"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700">Height (cm)</label>
                        <input type="number" id="height" placeholder="e.g., 170"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input type="number" id="weight" placeholder="e.g., 65"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>

                </div>

                <div class="flex justify-center items-start lg:pt-0">
                    {{-- {# Centered horizontally, aligned to top vertically. Added lg:pt-0 to remove top padding on
                    large screens. #} --}}
                    <img src="{{ asset('frontend-images/body-measurements-chart.png') }}" alt="Body Measurement Guide"
                        class="max-w-full h-auto rounded-md shadow-lg" />
                    {{-- {# Use a specific image for measurement guide. max-w-full and h-auto ensures it's responsive.
                    #}
                    {# **NOTE: You'll need to create or find a suitable 'body-measurements-guide.png' image and place it
                    in your 'frontend-images' folder.** #} --}}
                </div>
            </div>

            <div class="pt-8 flex justify-end">
                <button
                    class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-300">
                    Save Measurements
                </button>
            </div>
        </div>
        <div id="my-bookings" class="content-section hidden">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">My Bookings</h1>
            <p class="text-gray-600 pb-8">View and manage your upcoming and past bookings.</p>

            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                        <tr>
                            <th scope="col" class="py-3 px-6">
                                Booking Date
                            </th>
                            <th scope="col" class="py-3 px-6">
                                Item
                            </th>
                            <th scope="col" class="py-3 px-6">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white border-b  hover:bg-gray-50 ">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap ">
                                August 25, 2025
                            </td>
                            <td class="py-4 px-6 text-gray-900">
                                Elegant Black Gown
                            </td>
                            <td class="py-4 px-6">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-green-200 dark:text-green-900">Confirmed</span>
                            </td>
                        </tr>
                        <tr class="bg-white border-b  hover:bg-gray-50 ">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap ">
                                September 1, 2025
                            </td>
                            <td class="py-4 px-6 text-gray-900">
                                Classic Navy Suit
                            </td>
                            <td class="py-4 px-6">
                                <span
                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-yellow-200 dark:text-yellow-900">Pending</span>
                            </td>
                        </tr>
                        <tr class="bg-white  hover:bg-gray-50 ">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap ">
                                August 10, 2025
                            </td>
                            <td class="py-4 px-6 text-gray-900">
                                Red Evening Dress
                            </td>
                            <td class="py-4 px-6">
                                <span
                                    class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-gray-200 dark:text-gray-900">Completed</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- {# Placeholder for other sections (e.g., My Orders, etc.) #} --}}
        <div id="my-orders" class="content-section hidden">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">My Orders</h1>
            <p class="text-gray-600 pb-8">View your past and current orders.</p>
            <div class="border p-4 text-gray-500">No orders found.</div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- My Account Dropdown Logic ---
        const myAccountDropdownBtn = document.getElementById('myAccountDropdownBtn');
        const myAccountDropdownContent = document.getElementById('myAccountDropdownContent');
        const dropdownArrow = document.getElementById('dropdownArrow');

        myAccountDropdownBtn.addEventListener('click', () => {
            myAccountDropdownContent.classList.toggle('hidden');
            myAccountDropdownContent.classList.toggle('flex'); // Add flex to make it a flex column
            dropdownArrow.classList.toggle('rotate-180'); // Rotate arrow on toggle
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', (event) => {
            if (!myAccountDropdownBtn.contains(event.target) && !myAccountDropdownContent.contains(event.target)) {
                if (!myAccountDropdownContent.classList.contains('hidden')) {
                    myAccountDropdownContent.classList.add('hidden');
                    myAccountDropdownContent.classList.remove('flex');
                    dropdownArrow.classList.remove('rotate-180');
                }
            }
        });


        // --- Tab / Section Switching Logic ---
        const navLinks = document.querySelectorAll('.nav-link');
        const contentSections = document.querySelectorAll('.content-section');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent default link behavior (page reload)

                // Remove 'active' class from all nav links and add to clicked one
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                // Hide all content sections
                contentSections.forEach(section => section.classList.add('hidden'));

                // Show the target content section
                const targetId = link.dataset.target; // Get the data-target attribute value
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }

                // Optional: Close dropdown after selection on smaller screens
                if (window.innerWidth < 1024) { // Adjust breakpoint as needed
                    myAccountDropdownContent.classList.add('hidden');
                    myAccountDropdownContent.classList.remove('flex');
                    dropdownArrow.classList.remove('rotate-180');
                }
            });
        });

        // Trigger click on the initially active link to display its content
        document.querySelector('.nav-link.active')?.click();
    });
</script>

<style>
    /* Add this to your CSS for the active link style */
    .nav-link.active {
        color: #8b5cf6;
        /* A slightly darker purple for active state */
        font-weight: 600;
        /* Make it semi-bold */
    }
</style>