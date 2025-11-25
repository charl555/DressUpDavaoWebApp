<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Terms of Use</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white h-full">
    <main>
        <x-navbar />
        <div class="py-40 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Title Section -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6" style="font-family: 'Playfair Display', serif;">
                        Terms of Use
                    </h2>
                </div>

                <!-- Terms of Use Content -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 text-center">
                    <div class="max-w-2xl mx-auto">
                        {{-- <div
                            class="bg-gradient-to-br from-purple-100 to-indigo-100 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div> --}}
                        <h3 class="text-2xl font-bold text-gray-900 mb-6"
                            style="font-family: 'Playfair Display', serif;">
                            Agreement to Terms
                        </h3>
                        <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                            By accessing DressUp Davao, you agree to:
                        </p>
                        <div class="space-y-4 text-gray-600 text-lg">
                            <p class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Use the platform responsibly
                            </p>
                            <p class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Respect all users and shop owners
                            </p>
                            <p class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Not engage in harmful or fraudulent activity
                            </p>
                            <p class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Follow our content and rental guidelines
                            </p>
                        </div>
                        <div class="mt-8 p-4 bg-red-50 rounded-lg border border-red-200">
                            <p class="text-red-700 font-medium">
                                If you do not agree, please discontinue using the site.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-footer />

</body>

</html>